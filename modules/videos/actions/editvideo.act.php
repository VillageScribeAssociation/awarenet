<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a video
//--------------------------------------------------------------------------------------------------
//	needs the video's UID/alias and optionally /return_uploadmultiple
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$model = new Videos_Video($req->ref);
	if (false == $model->loaded) { $page->do404('Video not found.'); }
	//if ('' == $model->fileName) { $page->do404(); }
	if (false == $user->authHas($model->refModule, $model->refModel, 'videos-edit', $model->refUID))
		{ $page->do403('You are not authorized to edit this video.'); }
	
	//TODO: add more auth options here

	$return = '';
	if (true == array_key_exists('return', $req->args)) { $return = $req->args['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$page->load('modules/videos/actions/editvideo.if.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['return'] = $return;
	$page->render();

?>
