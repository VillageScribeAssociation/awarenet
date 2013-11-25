<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/utils.inc.php');

//--------------------------------------------------------------------------------------------------
//*	rotate an image 90 degrees left or right
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID of an images_image object [string]
//postopt: direction - direction to rotate (clockwise|anticlockwise) [string]
//postopt: return - optional return url [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $_POST)) { $page->do403('Image UID not given.'); }
	if (false == array_key_exists('direction', $_POST)) { $page->do403('Direction not given.'); }

	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { $page->do404("Image not found."); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'images-add', $model->refUID)) {
		$page->do403('You are not authorized to edit this image.'); 
	}

	if (false == $kapenta->fs->exists($model->fileName)) { $page->do404('File missing.'); }

	$angle = 270;
	if (
		(true == array_key_exists('direction', $_POST)) &&
		('anticlockwise' == $_POST['direction']))
	{ $angle = 90;	}

	//----------------------------------------------------------------------------------------------
	//	rotate the image
	//----------------------------------------------------------------------------------------------


	foreach($model->transforms->members as $label => $fileName) {
		if ('' !== $fileName) {
			$check = $kapenta->fileDelete($fileName, true);
			if (false == $check) {
				$msg = ''
				 . "Could not delete tranform of image: " . $model->title
				 . " (" . $model->UID . "):<br/>" . $fileName;
				$session->msg($msg);
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	rotate the image // NOTE: this is a lossy operation for jpegs
	//----------------------------------------------------------------------------------------------
	//header("Content-type: image/jpeg");

	$source = imagecreatefromjpeg($model->fileName);
	$rotate = images_rotate($source, $angle, 0);

	imagejpeg($rotate, $model->fileName, $angle);

	$model->hash = $kapenta->fileSha1($model->fileName);
	$report = $model->save();
	
	if ('' == $report) {
		$session->msg("Rotated image.", 'ok');
	} else {
		$session->msg("Error while rotating image:<br/>" . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to images module
	//----------------------------------------------------------------------------------------------
	$page->do302('images/show/' . $model->alias);

?>
