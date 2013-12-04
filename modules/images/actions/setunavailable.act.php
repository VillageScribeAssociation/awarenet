<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	change the 'unavailable' image
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID or alias of an Images_Image object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Image not specified'); }

	$image = new Images_Image($_POST['UID']);
	if (false == $image->loaded) { $page->do404('Image not found.'); }

	$image->transforms->loadImage();
	if (-1 == $image->transforms->image) {
		$session->msg('Cannot create transforms from this image.', 'bad');
		$page->do302('images/settings/');
	}

	//----------------------------------------------------------------------------------------------
	//	delete current 'unavailable' images
	//----------------------------------------------------------------------------------------------
	$kapenta->fs->makePath('data/images/unavailable/na.txt');
	$oldFiles = $kapenta->listFiles('data/images/unavailable/', '.jpg');
	foreach($oldFiles as $oldFile) {
		$check = $kapenta->fs->delete('data/images/unavailable/' . $oldFile);
		if (true == $check) { $session->msg("Removed: $oldFile", 'ok'); }
		else { $session->msg("Cannot remove: $oldFile<br/>", 'bad'); }
	}

	//----------------------------------------------------------------------------------------------
	//	make and copy all transforms
	//----------------------------------------------------------------------------------------------

	foreach($image->transforms->presets as $preset) {
		$check = $image->transforms->make($preset['label']);
		if ((true == $check) && ('full' !== $preset['label'])) {
			$srcFile = $image->transforms->members[$preset['label']];
			$destFile = 'data/images/unavailable/unavailable_' . $preset['label'] . '.jpg';
			$session->msg("src: $srcFile<br/>\ndest: $destFile<br/>\n");
			$check = $kapenta->fs->copy($srcFile, $destFile);
			if (false == $check) { $session->msg("<b>Error:</b> Could not copy $srcFile", 'bad'); }
		} else {
			$session->msg("Could not create transform: " . $preset['label'] . "<br/>\n", 'bad');
		}
	}

	$srcFile = $image->fileName;
	$destFile = 'data/images/unavailable/unavailable.jpg';
	$session->msg("src: $srcFile<br/>\ndest: $destFile<br/>\n");
	$check = $kapenta->fs->copy($srcFile, $destFile);
	if (false == $check) { $session->msg("<b>Error:</b> Could not copy $srcFile", 'bad'); }

	$srcFile = $image->fileName;
	$destFile = 'data/images/unavailable/unavailable_full.jpg';
	$session->msg("src: $srcFile<br/>\ndest: $destFile<br/>\n");
	$check = $kapenta->fs->copy($srcFile, $destFile);
	if (false == $check) { $session->msg("<b>Error:</b> Could not copy $srcFile", 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to image settings page
	//----------------------------------------------------------------------------------------------
	$page->do302('images/settings/');

?>
