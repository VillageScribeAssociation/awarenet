<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a video
//--------------------------------------------------------------------------------------------------
//	needs the video's UID/alias and optionally /return_uploadmultiple
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Video not found.'); }
	//if ('' == $model->fileName) { $kapenta->page->do404(); }
	if (false == $kapenta->user->authHas($model->refModule, $model->refModel, 'videos-edit', $model->refUID))
		{ $kapenta->page->do403('You are not authorized to edit this video.'); }
	
	//TODO: add more auth options here

	$return = '';
	if (true == array_key_exists('return', $kapenta->request->args)) { $return = $kapenta->request->args['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/videos/actions/editvideo.if.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['return'] = $return;
	$kapenta->page->render();

?>
