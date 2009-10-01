<?

//--------------------------------------------------------------------------------------------------
//	page for downloading images and associating them with records
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/images/models/image.mod.php');

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; $refUID = ''; $return = ''; $URL = '';
	if (array_key_exists('refModule', $_POST)) { $refModule = sqlMarkup($_POST['refModule']); }
	if (array_key_exists('refUID', $_POST)) { $refUID = sqlMarkup($_POST['refUID']); }
	if (array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (array_key_exists('URL', $_POST)) { $URL = $_POST['URL']; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = ''; $raw = ''; $img = false; $imgName = '';
	
	if (($refUID == '') OR ($refModule == '')) { $msg = "(missing arguments to image download)"; }
	if (($msg = '') AND (authHas($refModule, 'images', '') == false)) { $msg = "(not authorised)";}

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------

	if ($msg == '') {
		$raw = @implode(file($URL));
		if ($raw == false) { $msg = "Image could not be downloaded, check the URL?"; }
	}

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	if ($msg == '') { 
		$img = @imagecreatefromstring($raw); 
	}
	if (($msg == '') AND ($img == false)) { $msg = "Could not validate image."; }
	
	//----------------------------------------------------------------------------------------------
	//	get image name
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$imgName = strtolower(basename($URL));
		$imgName = str_replace('.jpg', '', $imgName);
		$imgName = str_replace('.jpeg', '', $imgName);
		$imgName = str_replace('.png', '', $imgName);
		$imgName = str_replace('.gif', '', $imgName);
		if ($imgName == '') { $imgName = createUID() . '.jpg'; }
	}

	//----------------------------------------------------------------------------------------------
	//	create image record and save file
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$i = new Image();
		$i->data['refUID'] = $refUID;
		$i->data['refModule'] = $refModule;
		$i->data['title'] = $imgName;
		$i->storeFile($img);
		$i->data['licence'] = 'unknown';
		$i->data['attribURL'] = $URL;
		$i->data['weight'] = '0';
		$ext = $i->extArray();
		$i->save();
		$msg = "Downloaded image: $URL <br/>\n";

		//------------------------------------------------------------------------------------------
		//	check if a images_add callback can be sent to this refModule
		//------------------------------------------------------------------------------------------

		$callBackFile = $installPath . 'modules/' . $refModule . '/callbacks.inc.php';
		$callBackFn = $refModule . '__cb_images_add';
		if (file_exists($callBackFile) == true) {
			require_once($callBackFile);
			if (function_exists($callBackFn) == true) {
				//----------------------------------------------------------------------------------
				//	send the callback
				//----------------------------------------------------------------------------------
				$callBackFn($refUID, $ext['UID'], $ext['title']);

			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
		
	$_SESSION['sMessage'] .= $msg;
	if ($return = 'uploadmultiple') {
		do302('images/uploadmultiple/refModule_' . $refModule . '/refUID_' . $refUID . '/');
	}

?>
