<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show an image from a users gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	anyone can view images TODO: add permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('images_image');
	$model = new Images_Image($UID);
	if (false == $model->loaded) { $page->do404('Image not found.'); }

	//----------------------------------------------------------------------------------------------
	//	load models
	//----------------------------------------------------------------------------------------------
	$userRa = $aliases->getDefault('users_user', $model->createdBy);
	$gallery = new Gallery_Gallery($model->refUID);
	//if (false == $gallery->loaded) { $page->do404('Gallery not found.'); } TODO

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/gallery/actions/image.page.php');
	$kapenta->page->blockArgs['imageUID'] = $UID;
	$kapenta->page->blockArgs['imageRa'] = $model->alias;
	$kapenta->page->blockArgs['imageTitle'] = $model->title;
	$kapenta->page->blockArgs['userUID'] = $model->createdBy;
	$kapenta->page->blockArgs['userRa'] = $userRa;
	$kapenta->page->blockArgs['galleryUID'] = $model->refUID;
	$kapenta->page->blockArgs['galleryTitle'] = $gallery->title;
	$kapenta->page->blockArgs['galleryRa'] = $gallery->alias;
	$kapenta->page->render();

?>
