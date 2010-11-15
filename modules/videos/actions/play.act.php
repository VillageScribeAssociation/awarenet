<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	play an flv or mp4 video using flowplayer 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Video not specified.'); }

	$model = new Videos_Video($req->ref);
	if (false == $model->loaded) { $page->do404('Video not found.'); }
	//TODO: permissions check here

	if ('swf' == $model->format) { $page->do302('videos/animate/' . $model->alias); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/videos/actions/play.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['title'] = $model->title;
	$page->blockArgs['caption'] = $model->caption;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
