<?

//--------------------------------------------------------------------------------------------------
//	show an image from a users gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	anyone can view images TODO: add permissions
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	$imageUID = raGetOwner($request['ref'], 'images');
	if ($imageUID == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	load models
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/images/models/image.mod.php');
	$model = new Image($imageUID);
	$userRa = raGetDefault('users', $model->data['createdBy']);

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');
	$gallery = new Gallery($model->data['refUID']);

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/gallery/actions/image.page.php');
	$page->blockArgs['imageUID'] = $imageUID;
	$page->blockArgs['imageRa'] = $model->data['recordAlias'];
	$page->blockArgs['imageTitle'] = $model->data['title'];
	$page->blockArgs['userUID'] = $model->data['createdBy'];
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryUID'] = $model->data['refUID'];
	$page->blockArgs['galleryTitle'] = $gallery->data['title'];
	$page->blockArgs['galleryRa'] = $gallery->data['recordAlias'];
	$page->render();

?>
