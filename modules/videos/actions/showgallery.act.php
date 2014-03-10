<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a video gallery page
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }							// check ref
	$UID = $aliases->findRedirect('videos_gallery'); 					// check correct ref
	
	$model = new Videos_Gallery($kapenta->request->ref);	
	if (false == $model->loaded) { $kapenta->page->do404('Video gallery not found.'); }
	if (false == $user->authHas('videos', 'videos_gallery', 'show', $model->UID)) {$kapenta->page->do403();}

	//----------------------------------------------------------------------------------------------
	//	make the page
	//----------------------------------------------------------------------------------------------
	$userRa = $aliases->getDefault('users_user', $model->createdBy);

	$kapenta->page->load('modules/videos/actions/showgallery.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['userUID'] = $model->createdBy;
	$kapenta->page->blockArgs['userRa'] = $userRa;
	$kapenta->page->blockArgs['galleryRa'] = $model->alias;
	$kapenta->page->blockArgs['galleryTitle'] = $model->title;
	$kapenta->page->render();

?>
