<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	play an flv or mp4 video using flowplayer 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Animation not specified.'); }

	$model = new Videos_Video($req->ref);
	if (false == $model->loaded) { $page->do404('Animation not found.'); }
	//TODO: permissions check here

	if ('swf' != $model->format) { $page->do302('videos/play/' . $model->alias); }

	$navReturn = "
		<table noborder>
		  <tr>
			<td>
				<img src='%%serverPath%%themes/%%defaultTheme%%/icons/arrow_left.jpg' />
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

	$page->load('modules/videos/actions/animate.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['title'] = $model->title;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
