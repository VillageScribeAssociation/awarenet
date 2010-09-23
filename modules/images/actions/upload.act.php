<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page for accepting upload of images and associating them with records
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; 
	$refModel = ''; 
	$refUID = ''; 
	$category = ''; 
	$return = ''; 
	$returnUrl = ''; 
	$tempFile = ''; 
	$srcName = '';
	$nofile = false;	

	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('category', $_POST)) { $category = $_POST['category']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';
	
	if (('' == $refUID) OR ('' == $refModule) OR ('' == $refModel)) 
		{ $page->do404('(missing arguments to image upload)', true); }

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
			//TODO
			break;

		default:
			$page->do404('unknown return argument', true);
			break;
	}

	//TODO: chck this permission name
	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) {
		if ('xml' == $return) { $page->doXmlError('Not authorized.'); }
		$session->msg('You are not authorised to add images to this item.', 'bad');
		$page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('userfile', $_FILES)) {
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		if (true == file_exists($tempFile)) {
			$raw = @implode(@file($tempFile));
		} else { 
			$raw = ''; 
			if ('xml' == $return) { $page->doXmlError('No file uploaded.'); }
			$session->msg('No file uploaded.', 'bad'); 
			$page->do302($returnUrl);			
		}
	
	} else { 
		$session->msg('No file uploaded.', 'bad'); 
		$page->do302($returnUrl);
	}
	

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	$img = false;
	if ('' != $raw) { $img = @imagecreatefromstring($raw); }
	if (false == $img) {
		if ('xml' == $return) { $page->doXmlError('Could not validate image.'); }
		$session->msg('Could not validate image.', 'bad'); 
		$page->do302($returnUrl);
	}
	
	//----------------------------------------------------------------------------------------------
	//	get image name
	//----------------------------------------------------------------------------------------------
	$imgName = strtolower($srcName);
	$imgName = str_replace('.jpg', '', $imgName);
	$imgName = str_replace('.jpeg', '', $imgName);
	$imgName = str_replace('.png', '', $imgName);
	$imgName = str_replace('.gif', '', $imgName);
	if ('' == $imgName) { $imgName = $kapenta->createUID() . '.jpg'; }

	//----------------------------------------------------------------------------------------------
	//	handle upload of single images (delete any others before saving)
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('uploadSingleImage' == $_POST['action'])) {

		//$sql = "select * from Images_Image "
		//	 . "where refUID='" . $db->addMarkup($refUID) . "'"
		// . " and refModule='" . $sb->addMarkup($refModule) . "'";

		$conditions = array();
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	
		$range = $db->loadRange('Images_Image', '*', $conditions);
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
	/*	
	$args = array(	'refModule' => $refModule, 
					'refUID' => $refUID, 
					'imageUID' => $ext['UID'], 
					'imageTitle' => $ext['title']    );

	eventSendSingle($refModule, 'images_added', $args);
	*/	// TODO: this

	//----------------------------------------------------------------------------------------------
	//	return xml or redirect back 
	//----------------------------------------------------------------------------------------------
	
	if ($return == 'xml') {
		//--------------------------------------------------------------------------------------
		//	image saved
		//--------------------------------------------------------------------------------------
		echo $model->toXml(true, '', true);
		die();
	}

	//echo "returnURL: $returnUrl <br/>\n";

	if ('' == $report) { $report = "Uploaded image: $srcName <br/>\n"; }
	$session->msg($report, 'ok');
	$page->do302($returnUrl);

?>
