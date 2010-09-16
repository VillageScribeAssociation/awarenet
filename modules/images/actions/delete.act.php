<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//*	delete an image and associated record, derivative files, etc
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Image not specified (UID).'); }

	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Image not found.'); }

	//TODO: add other permissions here?
	if (false == $user->authHas($model->refModule, $model->refModel, 'images-delete', $model->refUID)) 
		{ $page->do403('You cannot delete this image.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the image
	//----------------------------------------------------------------------------------------------
	$kapenta->logSync("deleteing image " . $model->UID . "via form on images module.\n");
	$model->delete();
	
	if (true == array_key_exists('return', $_POST)) {
		if ('xml' == $_POST['return']) {
			echo "<?xml version=\"1.0\"?>\n";
			echo "<notice>Image " . $model->UID . " deleted</notice>\n";
			die();

		} else { $page->do302($_POST['return']); }
	}

	$page->do302('/images/');

?>
