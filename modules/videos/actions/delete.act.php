<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	//require_once($kapenta->installPath . 'modules/videos/inc/videos__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Videos_Video object, cover image, etc
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Video not specified.', true); }

	$model = new Videos_Video($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Video not found.', true); }

	//TODO: add other permissions here?
	$auth = false;

	if ($user->UID == $model->createdBy) { $auth = true; }

	if (true == $user->authHas($model->refModule, $model->refModel, 'videos-remove', $model->refUID)) {
		$auth = true;
	}

	if (false == $auth) { $kapenta->page->do403('You cannot delete this video.', true); }

	//----------------------------------------------------------------------------------------------
	//	delete the image
	//----------------------------------------------------------------------------------------------
	$kapenta->logSync("deleteing video " . $model->UID . "via form on videos module.\n");
	$model->delete();
	
	if (true == array_key_exists('return', $_POST)) {
		if ('xml' == $_POST['return']) {
			echo "<?xml version=\"1.0\"?>\n";
			echo "<notice>Image " . $model->UID . " deleted</notice>\n";
			die();

		} else { $kapenta->page->do302($_POST['return']); }
	}

	$kapenta->page->do302('/videos/');

?>
