<?

//--------------------------------------------------------------------------------------------------
//	page for accepting upload of images and associating them with records
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/images/models/image.mod.php');

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; $refUID = ''; $category = ''; $return = ''; $tempFile = ''; $srcName = '';
	if (array_key_exists('refModule', $_POST)) { $refModule = sqlMarkup($_POST['refModule']); }
	if (array_key_exists('refUID', $_POST)) { $refUID = sqlMarkup($_POST['refUID']); }
	if (array_key_exists('category', $_POST)) { $category = $_POST['category']; }
	if (array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	$nofile = false;	

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = ''; $raw = ''; $img = false; $imgName = '';
	
	if (($refUID == '') OR ($refModule == '')) { $msg = "(missing arguments to image download)"; }
	if (($msg = '') AND (authHas($refModule, 'images', '') == false)) { $msg = "(not authorised)"; }

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------
	if (($msg == '') AND (array_key_exists('userfile', $_FILES))) {
	
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		if (file_exists($tempFile) == true) {
			$raw = @implode(@file($tempFile));
		} else { 
			$raw = ''; $msg = '(no file uploaded) '; 
		}
	
	} else { $msg = '(no file uploaded) '; }
	

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	$img = false;
	if (($msg == '') AND ($raw != '')) { 
		$img = @imagecreatefromstring($raw); 
	}
	if ($img == false) { $msg = "Could not validate image."; }
	
	//----------------------------------------------------------------------------------------------
	//	get image name
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$imgName = strtolower($srcName);
		$imgName = str_replace('.jpg', '', $imgName);
		$imgName = str_replace('.jpeg', '', $imgName);
		$imgName = str_replace('.png', '', $imgName);
		$imgName = str_replace('.gif', '', $imgName);
		if ($imgName == '') { $imgName = createUID() . '.jpg'; }
	}

	//----------------------------------------------------------------------------------------------
	//	handle upload of single images (delete any others before saving)
	//----------------------------------------------------------------------------------------------
	if ((array_key_exists('action', $_POST)) && ($_POST['action'] == 'uploadSingleImage')) {
		$sql = "select * from images "
			 . "where refUID='" . $refUID . "' and refModule='" . $refModule . "'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$oldImg = new Image($row['UID']);
			$oldImg->delete();
		}
		
	}

	//----------------------------------------------------------------------------------------------
	//	create image record and save file
	//----------------------------------------------------------------------------------------------
	$i = new Image();
	if ($msg == '') {
		$i->data['refUID'] = $refUID;
		$i->data['refModule'] = $refModule;
		$i->data['title'] = $imgName;
		$i->storeFile($img);
		$i->data['licence'] = 'unknown';
		$i->data['attribURL'] = $URL;
		$i->data['category'] = $category;
		$i->data['weight'] = '0';
		$ext = $i->extArray();
		$i->save();
		$msg = "Uploaded image: $srcName <br/>\n";

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
	//	return xml or redirect back 
	//----------------------------------------------------------------------------------------------
	
	if ($return == 'xml') {
		if ($msg == '') {
			//--------------------------------------------------------------------------------------
			//	image saved
			//--------------------------------------------------------------------------------------
			echo "<?xml version=\"1.0\"?>\n";
			echo arrayToXml2d($i->data, 'image', '');
			die();

		} else {
			//--------------------------------------------------------------------------------------
			//	image not saved
			//--------------------------------------------------------------------------------------
			echo "<?xml version=\"1.0\"?>\n";
			echo "<error>$msg</error>";
			die();
		}
	}

	$_SESSION['sMessage'] .= $msg;
	if ($return == 'uploadmultiple') {
		do302('images/uploadmultiple/refModule_' . $refModule . '/refUID_' . $refUID . '/');
	}

	if ($return == 'uploadsingle') {
		do302('images/uploadsingle/refModule_' . $refModule . '/refUID_' . $refUID . '/category_' . $category . '/');
	}

?>
