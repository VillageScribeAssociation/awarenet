<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show an image from a users gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	anyone can view images TODO: add permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Images_Image');
	$model = new Images_Image($UID);

	//----------------------------------------------------------------------------------------------
	//	load models
	//----------------------------------------------------------------------------------------------
	$userRa = $aliases->getDefault('Users_User', $model->createdBy);
	$gallery = new Gallery_Gallery($model->refUID);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/gallery/actions/image.page.php');
	$page->blockArgs['imageUID'] = $UID;
	$page->blockArgs['imageRa'] = $model->alias;
	$page->blockArgs['imageTitle'] = $model->title;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryUID'] = $model->refUID;
	$page->blockArgs['galleryTitle'] = $gallery->title;
	$page->blockArgs['galleryRa'] = $gallery->alias;
	$page->render();

?>
