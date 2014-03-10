<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display an image at its original size
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$model = new Images_Image($kapenta->request->ref);
	if ($model->fileName == '') { $kapenta->page->do404(); }
			
	//----------------------------------------------------------------------------------------------
	//	return the original file
	//----------------------------------------------------------------------------------------------
	if (true == $kapenta->fs->exists($model->fileName)) {
		// transform exists
		header('Content-Type: image/jpeg');
		readfile($kapenta->installPath . $model->fileName);

	} else {
		// original file missing, send placeholder image
		header('Content-Type: image/jpeg');
		//$sync->requestFile($i->fileName);

		readfile(
			$kapenta->installPath . 'data/images/unavailable/unavailable_widthmax.jpg'
		);

	}

?>
