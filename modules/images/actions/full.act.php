<?

//--------------------------------------------------------------------------------------------------
//	display an image at its original size
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/images/models/image.mod.php');
	if ($request['ref'] == '') { do404(); }
	$i = new Image($request['ref']);
	if ($i->data['fileName'] == '') { do404(); }
			
	//----------------------------------------------------------------------------------------------
	//	return the transform
	//----------------------------------------------------------------------------------------------
	if (file_exists($installPath . $i->data['fileName'])) {
		header('Content-Type: image/jpeg');
		readfile($installPath . $i->data['fileName']);
	} else {
		header('Content-Type: image/jpeg');
		syncRequestFile($i->data['fileName']);
		readfile($installPath . 'themes/' . $defaultTheme . '/unavailable/loading.jpg');
	}

?>
