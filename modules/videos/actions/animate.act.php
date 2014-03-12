<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	play an flv or mp4 video using flowplayer 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Animation not specified.'); }

	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Animation not found.'); }
	//TODO: permissions check here

	if ('swf' != $model->format) { $kapenta->page->do302('videos/play/' . $model->alias); }

	$navReturn = "
		<table noborder>
		  <tr>
			<td>
				<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/arrow_left.jpg' />
			</td>
			<td>
			<b>Return to video gallery.</b>
			</td>
		  </tr>
		</table>
	";

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/videos/actions/animate.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['title'] = $model->title;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
