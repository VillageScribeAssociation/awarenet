<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	recieve files uploaed via AJAX
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; 
	$refModel = ''; 
	$refUID = ''; 
	$category = ''; 
	$return = ''; 
	$tempFile = ''; 
	$srcName = '';
	$nofile = false;
	$tags = 'no';

	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

	if (false == array_key_exists('contents', $_POST)) { die('missing variable: contents'); }
	if (false == array_key_exists('fileName', $_POST)) { die('missing variable: fileName'); }
	if (false == array_key_exists('refModule', $_POST)) { die('missing variable: refModule'); }
	if (false == array_key_exists('refModel', $_POST)) { die('missing variable: refModel'); }
	if (false == array_key_exists('refUID', $_POST)) { die('missing variable: refUID'); }

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';

	if (('' == $refUID) OR ('' == $refModule) OR ('' == $refModel)) { 
		print_r($_POST);
		die('missing arguments to image upload'); 
	}

	//TODO: check this permission name
	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) {
		if ('xml' == $return) { $page->doXmlError('Not authorized.'); }
		die('You are not authorised to add images to this item.');
	}

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------
	$raw = $_POST['contents'];
	$startPos = strpos($raw, ',');
	$raw = substr($raw, $startPos + 1);
	$raw = str_replace(' ', '+', $raw);
	$raw = base64_decode($raw);

	//$temp = $kapenta->installPath . 'modules/images/temp/' . $_POST['fileName'];
	//$fH = fopen($temp, 'wb+');
	//fwrite($fH, $data);
	//fclose($fH);
	//echo "writing file...\n";

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	$img = false;
	if ('' != $raw) { $img = @imagecreatefromstring($raw); }
	if (false == $img) { die('Could not validate image.'); }
	
	//----------------------------------------------------------------------------------------------
	//	get image name
	//----------------------------------------------------------------------------------------------
	$imgName = $_POST['fileName'];
	$imgName = str_replace('.jpg', '', $imgName);
	$imgName = str_replace('.jpeg', '', $imgName);
	$imgName = str_replace('.png', '', $imgName);
	$imgName = str_replace('.gif', '', $imgName);
	if ('' == $imgName) { $imgName = $kapenta->createUID() . '.jpg'; }

	//----------------------------------------------------------------------------------------------
	//	handle upload of single images (delete any others before saving)
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('uploadSingleImage' == $_POST['action'])) {
		$conditions = array();
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	
		$range = $db->loadRange('images_image', '*', $conditions);
		foreach ($range as $row) {
			$oldImg = new Images_Image($row['UID']);
			$oldImg->delete();
		}
		
	}

	//----------------------------------------------------------------------------------------------
	//	create image record and save file
	//----------------------------------------------------------------------------------------------
	$model = new Images_Image();
	$model->refUID = $refUID;
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->title = $imgName;
	$model->storeFile($img);
	$model->licence = 'unknown';
	$model->attribURL = '';
	$model->category = $category;
	//NOTE: weight is set by $model->save()
	$ext = $model->extArray();
	$report = $model->save();

	//------------------------------------------------------------------------------------------
	//	send 'images_added' event to module whose record owns this image
	//------------------------------------------------------------------------------------------
	$args = array(
		'refModule' => $refModule,
		'refUID' => $refUID,
		'imageUID' => $ext['UID'],
		'imageTitle' => $ext['title']
	);

	$kapenta->raiseEvent('*', 'images_added', $args);

	//----------------------------------------------------------------------------------------------
	//	return xml or redirect back 
	//----------------------------------------------------------------------------------------------

	echo "(OK: " . $_POST['fileName'] . ")";

?>
