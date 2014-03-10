<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');
	//require_once($kapenta->installPath . 'modules/images/inc/images__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//*	delete an image and associated record, derivative files, etc
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->doXmlError('Image not specified.'); }

	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->doXmlError('Image not found.'); }

	//TODO: add other permissions here?
	$auth = false;

	if ($user->UID == $model->createdBy) { $auth = true; }

	if (true == $user->authHas($model->refModule, $model->refModel, 'images-remove', $model->refUID)) {
		$auth = true;
	}

	if (false == $auth) { $kapenta->page->doXmlError('You cannot delete this image.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the image
	//----------------------------------------------------------------------------------------------
	//$kapenta->logSync("deleting image " . $model->UID . "via form on images module.\n");
	$objAry = $model->toArray();
	$check = $model->delete();

	//----------------------------------------------------------------------------------------------
	//	reset weight on the the set
	//----------------------------------------------------------------------------------------------
	if (true == $check) {
		$session->msgAdmin('Reloading weight on sibling images.');
		$set = new Images_Images($objAry['refModule'], $objAry['refModel'], $objAry['refUID']);
		$set->checkWeights();
	}
	
	//----------------------------------------------------------------------------------------------
	//	done, redirect or return XML confirmation
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('return', $_POST)) {
		if ('xml' == $_POST['return']) {
			if (true == $check) {
				echo "<?xml version=\"1.0\"?>\n";
				echo "<notice>Image " . $model->UID . " deleted</notice>\n";
				die();
			} else { $kapenta->page->doXmlError('Could not delete image.'); }

		} else { $kapenta->page->do302($_POST['return']); }
	}

	$kapenta->page->do302('images/');

?>
