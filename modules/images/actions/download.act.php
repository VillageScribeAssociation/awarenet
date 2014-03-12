<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	downloads images and associating them with other objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = '';
	$refModel = '';  
	$refUID = '';
	$return = 'uploadmultiple';
	$URL = '';
	$tags = 'no';

	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (true == array_key_exists('URL', $_POST)) { $URL = $_POST['URL']; }
	if ((true == array_key_exists('tags', $_POST)) && ('yes' == $_POST['tags'])) { $tags = 'yes'; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';
	
	if (('' == $refUID) OR ('' == $refModule)  OR ('' == $refModel)) { 
		if ('xml' == $return) { $kapenta->page->doXmlError('Missing arguments to image download.'); }
		$kapenta->page->do404('Missing arguments to image download.', true); 
	}

	switch(strtolower($return)) {
		case 'uploadmultiple':
			$returnUrl = 'images/uploadmultiple/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/'
				. 'tags_' . $tags . '/';
			break;

		case 'uploadsingle':
			$returnUrl = 'images/uploadsingle/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/'
				. 'tags_' . $tags . '/';
			break;

		case 'xml':
			// TODO
			break;

		default:
			$kapenta->page->do404('unknown return argument', true);
			break;
	}

	if (false == $kapenta->user->authHas($refModule, $refModel, 'images-add', $refUID)) { 
		if ('xml' == $return) { $kapenta->page->doXmlError('Not authorized.'); }
		$kapenta->session->msg('You are not authorised to add images to this item.', 'bad');
		$kapenta->page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------

	$raw = '';
	if ($proxyEnabled == 'yes') {
		$raw = $utils->curlGet($URL, '');			// use HTTP proxy
	} else {
		$raw = @implode(file($URL));				// use file wrapper
	}

	if (false == $raw) { 
		if ('xml' == $return) { $kapenta->page->doXmlError('Image could not be downloaded.'); }
		$kapenta->session->msg('Image could not be downloaded, check the URL?', 'bad'); 
		$kapenta->page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	load as image
	//----------------------------------------------------------------------------------------------
	$img = @imagecreatefromstring($raw); 
	if (false == $img) {
		if ('xml' == $return) { $kapenta->page->doXmlError('Could not validate image.'); }
		$kapenta->session->msg('Could not validate image.', 'bad'); 
		$kapenta->page->do302($returnUrl);
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
	$report = $model->save();
	if ('' == $report) { $kapenta->session->msg('Downloaded image: ' . $URL, 'ok'); }
	else { $kapenta->session->msg('Could not save image: ' . $URL, 'bad'); }


	if ('' == $report) {
		//------------------------------------------------------------------------------------------
		//	send 'images_added' event to all modules
		//------------------------------------------------------------------------------------------
		$args = array(	'refModule' => $refModule, 
						'refUID' => $refUID, 
						'imageUID' => $ext['UID'], 
						'imageTitle' => $ext['title']
    	);
	
		$kapenta->raiseEvent('*', 'images_added', $args);			// send to all modules
		//$kapenta->raiseEvent($refModule, 'images_added', $args);	// send to owner module

		//------------------------------------------------------------------------------------------
		//	broadcast 'file_added' event (used by p2p, etc)
		//------------------------------------------------------------------------------------------
		$args = array(
			'refModule' => 'images', 
			'refModel' => 'images_image', 
			'refUID' => $model->UID, 
			'fileName' => $model->fileName, 
			'hash' => sha1_file($kapenta->installPath . $model->fileName),
			'size' => filesize($kapenta->installPath . $model->fileName)
		);

		$kapenta->raiseEvent('*', 'file_added', $args);

	}

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
		$kapenta->page->do302($returnUrl);
	}

?>
