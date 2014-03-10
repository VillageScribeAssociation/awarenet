<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	move a user image from one gallery to another
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user identity
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('moveImage' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('(Image) UID not given.'); }
	if (false == array_key_exists('gallery', $_POST)) { $kapenta->page->do404('(Gallery) UID not given.'); }

	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Image not found.'); }

	$gallery = new Gallery_Gallery($_POST['gallery']);
	if (false == $gallery->loaded) { $kapenta->page->do404('Gallery not found.'); }

	//----------------------------------------------------------------------------------------------
	//	move the image
	//----------------------------------------------------------------------------------------------
	$model->refModule = 'gallery';
	$model->refModel = 'gallery_gallery';
	$model->refUID = $gallery->UID;
	$report = $model->save();

	if ('' == $report) {
		$msg = "Moved image '" . $model->title . "' to gallery '" . $gallery->title . "'";
		$session->msg($msg, 'ok');
	} else {
		$session->msg("Could not move image '" . $model->title . "':<br/>" . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to new gallery
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('gallery/' . $gallery->alias);

?>
