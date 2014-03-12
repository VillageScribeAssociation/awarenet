<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a gallery page
//--------------------------------------------------------------------------------------------------
	
	//if ($kapenta->user->authHas('gallery', 'gallery_gallery', 'show', 'TODO:UIDHERE') == false) { $kapenta->page->do403(); }		// check basic permissions
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }							// check ref
	$UID = $aliases->findRedirect('gallery_gallery'); 					// check correct ref
	
	$model = new Gallery_Gallery($kapenta->request->ref);	

	$userRa = $aliases->getDefault('users_user', $model->createdBy);

	$kapenta->page->load('modules/gallery/actions/show.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['userUID'] = $model->createdBy;
	$kapenta->page->blockArgs['userRa'] = $userRa;
	$kapenta->page->blockArgs['galleryRa'] = $model->alias;
	$kapenta->page->blockArgs['galleryTitle'] = $model->title;
	$kapenta->page->render();

?>
