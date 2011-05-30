<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	accepts upload of videos and associates them with other objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	working variables
	//----------------------------------------------------------------------------------------------
	$refModule = '';				//%	name of a kapenta module [string]
	$refModel = '';					//%	type of object which owns this video [string]
	$refUID = '';					//%	unique ID of object which owns this video [string]
	$category = ''; 				//%	not used as yet, for grouping into playlists, etc [string]
	$return = ''; 					//% assume 'uploadmultiple' for now [string]
	$returnUrl = ''; 				//%	relative to $kapenta->serverPath [string]

	$UID = $kapenta->createUID();	//%	unique ID of new Videos_Video object [string]
	$tempFile = ''; 				//%	location of uploaded file, absolute? [string]
	$srcName = '';					//%	name of file as it was uploaded [string]
	$format = '';					//%	best guess of container format [string]
	$nofile = false;				//%	set to true if noting uploaded [bool]
	$tags = 'no';					//%	same as for images, not yet implemented (yes|no) [string]

	$fileName = 'data/videos/'		//%	location of stored file relative to serverPath [string]
		. substr($UID, 0, 1) . '/' 
		. substr($UID, 1, 1) . '/'
		. substr($UID, 2, 1) . '/' . $UID;


	// check form vars
	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('category', $_POST)) { $category = $_POST['category']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if ((true == array_key_exists('tags', $_POST)) && ('yes' == $_POST['tags'])) { $tags = 'yes'; }

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';
	
	if ('' == $refUID) { $page->do404('(refUID not given or upload too large)', true); }
	if ('' == $refModule) { $page->do404('(refModule not given)', true); }
	if ('' == $refModel) { $page->do404('(refModel not given)', true); }

	switch(strtolower($return)) {
		case 'uploadmultiple':
			$returnUrl = 'videos/uploadmultiple/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/'
				. 'tags_' . $tags . '/';
			break;	//..............................................................................

		case 'uploadsingle':
			$returnUrl = 'videos/uploadsingle/'
				. 'refModule_' . $refModule . '/'
				. 'refModel_' . $refModel . '/'
				. 'refUID_' . $refUID . '/'
				. 'tags_' . $tags . '/';
			break;	//..............................................................................

		case 'xml':
			//TODO
			break;	//..............................................................................

		default:
			$page->do404('unknown return argument', true);
			break;
	}

	// check module
	if (false == $kapenta->moduleExists($refModule)) { 
		if ('xml' == $return) { $page->doXmlError('No such module.'); }
		$session->msg('No such module.', 'bad');
		$page->do302($returnUrl);
	}

	// check owner object
	if (false == $db->objectExists($refModel, $refUID)) { 
		if ('xml' == $return) { $page->doXmlError('No such owner obejct.'); }
		$session->msg('No such owner obejct.', 'bad');
		$page->do302($returnUrl);
	}

	// check permissions
	if (false == $user->authHas($refModule, $refModel, 'videos-add', $refUID)) {
		if ('xml' == $return) { $page->doXmlError('Not authorized.'); }
		$session->msg('You are not authorised to add videos to this item.', 'bad');
		$page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('userfile', $_FILES)) {
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		if (true == file_exists($tempFile)) {
			//--------------------------------------------------------------------------------------
			//	try guess file type
			//--------------------------------------------------------------------------------------
			$revName = strtolower(strrev($srcName));
			if (substr($revName, 0, 4) == 'vlf.') { $format = 'flv'; }
			if (substr($revName, 0, 4) == '4pm.') { $format = 'mp4'; }
			if (substr($revName, 0, 4) == 'fws.') { $format = 'swf'; }
			if (substr($revName, 0, 4) == '3pm.') { $format = 'mp3'; }
			if ('' == $format) {
				if ('xml' == $return) { $page->doXmlError('No file uploaded.'); }
				$session->msg('Format not supported (must be flv, swf, mp3 or mp4).', 'bad'); 
				$page->do302($returnUrl);
			}

			$fileName .= "." . $format;

			//--------------------------------------------------------------------------------------
			//	copy to location
			//--------------------------------------------------------------------------------------
			$kapenta->fileMakeSubdirs($fileName);

			$check = copy($tempFile, $kapenta->installPath . $fileName);
			if (false == $check) {
				$session->msg('Could not move image (disk full?).', 'bad'); 
				$page->do302($returnUrl);
			}
			unlink($tempFile);

			//--------------------------------------------------------------------------------------
			//	TODO: validate this video somehow (mplayer?) and try find its length
			//--------------------------------------------------------------------------------------
			
		} else {
			if ('xml' == $return) { $page->doXmlError('No file uploaded.'); }
			$session->msg('No file uploaded.', 'bad'); 
			$page->do302($returnUrl);			
		}
	
	} else { 
		$session->msg('No file uploaded.', 'bad'); 
		$page->do302($returnUrl);
	}
	
	//----------------------------------------------------------------------------------------------
	//	get video name
	//----------------------------------------------------------------------------------------------
	$vidName = strtolower($srcName);
	$vidName = str_replace('.flv', '', $vidName);
	$vidName = str_replace('.mp4', '', $vidName);
	$vidName = str_replace('.mp3', '', $vidName);
	//$vidName = str_replace('.png', '', $vidName);
	//$vidName = str_replace('.gif', '', $vidName);
	if ('' == $vidName) { $vidName = $kapenta->createUID() . '.jpg'; }

	//----------------------------------------------------------------------------------------------
	//	handle upload of single videos (delete any others before saving)
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('uploadSingleVideo' == $_POST['action'])) {

		$conditions = array();
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	
		$range = $db->loadRange('videos_video', '*', $conditions);
		foreach ($range as $row) {
			$oldVid = new Videos_Video($row['UID']);
			$oldVid->delete();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	create video object and save to database
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Video();
	$model->refUID = $refUID;
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->title = $vidName;
	$model->licence = 'unknown';
	$model->attribName = '';
	$model->attribUrl = '';
	$model->fileName = $fileName;
	$model->format = $format;
	$model->category = $category;
	//NOTE: weight is set by $model->save()  // TODO: this
	$ext = $model->extArray();
	$report = $model->save();

	if ('' == $report) {
		//------------------------------------------------------------------------------------------
		//	send 'videos_added' event to module to which owner belongs
		//------------------------------------------------------------------------------------------
		$args = array(
			'refModule' => $refModule, 
			'refModel' => $refModel, 
			'refUID' => $refUID, 
			'videoUID' => $model->UID, 
			'videoTitle' => $model->title
		);
		$kapenta->raiseEvent('*', 'videos_added', $args);

		//------------------------------------------------------------------------------------------
		//	return xml or redirect back 
		//------------------------------------------------------------------------------------------
	
		if ($return == 'xml') {
			//--------------------------------------------------------------------------------------
			//	video saved
			//--------------------------------------------------------------------------------------
			echo $model->toXml(true, '', true);
			die();
		}

		$session->msg("Uploaded video: $srcName", 'ok');
		$page->do302($returnUrl);

	} else {
		$session->msg("Could not save video object.", 'bad');
		$page->do302($returnUrl);
	}

?>
