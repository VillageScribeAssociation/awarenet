<?php

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to save an image sketch
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	if (false == array_key_exists('img64', $_POST)) { $page->doXmlError('Could not save image.'); }
	if (false == array_key_exists('title64', $_POST)) { $page->doXmlError('Could not save image.'); }


	//----------------------------------------------------------------------------------------------
	//	check that a 'My Sketches' gallery exists, create if not (user registry setting)
	//----------------------------------------------------------------------------------------------

	$model = new Gallery_Gallery();

	if ('' == $user->get('sketchpad.gallery')) {

		$ownerNameBlock = '[[:users::name::userUID=' . $user->UID . ':]]';
		$schoolNameBlock = '[[:schools::name::schoolUID=' . $user->school . ':]]';

		$model->title = 'My Sketches';
		$model->description = "Images I&quot;ve scribbled on.";
		$model->imagecount = 0;
		$model->ownerName = $theme->expandBlocks($ownerNameBlock);
		$model->schoolUID = $user->school;
		$model->schoolName = $theme->expandBlocks($schoolNameBlock);

		$report = $model->save();
		if ('' != $report) { $page->doXmlError($report); }

		$user->set('sketchpad.gallery', $model->UID);
	}
	
	$model->load($user->get('sketchpad.gallery'));
	if (false == $model->loaded) { $page->doXmlError('Could not find gallery.'); }

	//----------------------------------------------------------------------------------------------
	//	Convert submitted image and attach to gallery
	//----------------------------------------------------------------------------------------------

	$title = base64_decode($_POST['title64']);
	$raw = base64_decode(str_replace(' ', '+', $_POST['img64']));

	$fileName = 'data/temp/' . $kapenta->time() . '_sketch.png';
	$kapenta->fs->put($fileName, $raw);

	$fileNameTxt = 'data/temp/' . $kapenta->time() . '_sketch.png.txt';
	$kapenta->fs->put($fileNameTxt, $_POST['img64']);

	//----------------------------------------------------------------------------------------------
	//	raise file_uploaded event on owner module
	//----------------------------------------------------------------------------------------------
	//	note that owner module must do something with this file, the temp file will be deleted
	//	immediately.

	$errmsg = '';
	if ('' == $errmsg) {
		$args = array(
			'module' => 'images',
			'refModule' => 'gallery',
			'refModel' => 'gallery_gallery',
			'refUID' => $model->UID,
			'path' => $fileName,
			'srcName' => '/sketch/upload/' . $title,
			'name' => $title,
			'extension' => '.png',
			'hash' => $kapenta->fileSha1($fileName)
		);

		$outcome = $kapenta->raiseEvent('images', 'file_attach', $args);
		foreach($outcome as $mod => $eventError) { $errmsg .= $eventError; }
		if ('' != $errmsg) { $page->doXmlError($errmsg); }
	}

	echo 'gallery/' . $model->alias;
	die();

?>
