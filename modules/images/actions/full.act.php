<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display an image at its original size
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$model = new Images_Image($req->ref);
	if ($model->fileName == '') { $page->do404(); }
			
	//----------------------------------------------------------------------------------------------
	//	return the original file
	//----------------------------------------------------------------------------------------------
	if (true == $kapenta->fileExists($model->fileName)) {
		// transform exists
		header('Content-Type: image/jpeg');
		readfile($kapenta->installPath . $model->fileName);

	} else {
		// original file missing, send placeholder image
		header('Content-Type: image/jpeg');
		//$sync->requestFile($i->fileName);

		readfile(
			$kapenta->installPath . 'themes/' . $kapenta->defaultTheme . '/unavailable/loading.jpg'
		);

	}

?>
