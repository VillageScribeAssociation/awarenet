<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page for downloading images and associating them with records
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = '';
	$refModel = '';  
	$refUID = '';
	$return = 'uploadmultiple';
	$URL = '';

	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (true == array_key_exists('URL', $_POST)) { $URL = $_POST['URL']; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';
	
	if (('' == $refUID) OR ('' == $refModule)  OR ('' == $refModel)) { 
		if ('xml' == $return) { $page->doXmlError('Missing arguments to image download.'); }
		$page->do404('Missing arguments to image download.', true); 
	}

	switch(strtolower($return)) {
		case 'uploadmultiple':
			$returnUrl = 'images/uploadmultiple/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/';
			break;

		case 'uploadsingle':
			$returnUrl = 'images/uploadsingle/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/';
			break;

		case 'xml':
			// TODO
			break;

		default:
			$page->do404('unknown return argument', true);
			break;
	}

	if (false == $user->authHas($refModule, $refModel, 'addimages', $refUID)) { 
		if ('xml' == $return) { $page->doXmlError('Not authorized.'); }
		$session->msg('You are not authorised to add images to this item.', 'bad');
		$page->do302($return);
	}

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------

	$raw = '';
	if ($proxyEnabled == 'yes') {
		$raw = curlGet($URL, '');			// use HTTP proxy
	} else {
		$raw = @implode(file($URL));		// use file wrapper
	}

	if (false == $raw) { 
		if ('xml' == $return) { $page->doXmlError('Image could not be downloaded.'); }
		$session->msg('Image could not be downloaded, check the URL?', 'bad'); 
		$page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	$img = @imagecreatefromstring($raw); 
	if (false == $img) {
		if ('xml' == $return) { $page->doXmlError('Could not validate image.'); }
		$session->msg('Could not validate image.', 'bad'); 
		$page->do302($returnUrl);
	}
	
	//----------------------------------------------------------------------------------------------
	//	get image name
	//----------------------------------------------------------------------------------------------
	$imgName = strtolower(basename($URL));
	$imgName = str_replace('.jpg', '', $imgName);
	$imgName = str_replace('.jpeg', '', $imgName);
	$imgName = str_replace('.png', '', $imgName);
	$imgName = str_replace('.gif', '', $imgName);
	if ('' == $imgName) { $imgName = $kapenta->createUID() . '.jpg'; }

	//----------------------------------------------------------------------------------------------
	//	create image record and save file
	//----------------------------------------------------------------------------------------------
	$model = new Images_Image();
	$model->refUID = $refUID;
	$model->refModel = $refModel;
	$model->refModule = $refModule;
	$model->title = $imgName;
	$model->storeFile($img);
	$model->licence = 'unknown';
	$model->attribURL = $URL;
	//NOTE: weight is set by $model->save()
	$ext = $model->extArray();
	$model->save();
	$session->msg("Downloaded image: $URL", 'ok');

	//------------------------------------------------------------------------------------------
	//	send 'images_added' event to module whose record owns this image TODO
	//------------------------------------------------------------------------------------------
	
	//$args = array(	'refModule' => $refModule, 
	//				'refUID' => $refUID, 
	//				'imageUID' => $ext['UID'], 
	//				'imageTitle' => $ext['title']    );
	//
	//eventSendSingle($refModule, 'images_added', $args);

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
		
	if ($return == 'xml') {
		//------------------------------------------------------------------------------------------
		//	image saved
		//------------------------------------------------------------------------------------------
		echo $model->toXml(true, '', true);
		die();
	} else {
		$page->do302($returnUrl);
	}

?>
