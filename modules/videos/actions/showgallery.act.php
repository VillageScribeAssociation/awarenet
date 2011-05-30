<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a video gallery page
//--------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }							// check ref
	$UID = $aliases->findRedirect('videos_gallery'); 					// check correct ref
	
	$model = new Videos_Gallery($req->ref);	
	if (false == $model->loaded) { $page->do404('Video gallery not found.'); }
	if (false == $user->authHas('videos', 'videos_gallery', 'show', $model->UID)) {$page->do403();}

	//----------------------------------------------------------------------------------------------
	//	make the page
	//----------------------------------------------------------------------------------------------
	$userRa = $aliases->getDefault('users_user', $model->createdBy);

	$page->load('modules/videos/actions/showgallery.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryRa'] = $model->alias;
	$page->blockArgs['galleryTitle'] = $model->title;
	$page->render();

?>
