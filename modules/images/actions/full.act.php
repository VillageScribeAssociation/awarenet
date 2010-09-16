<?

//--------------------------------------------------------------------------------------------------
//	display an image at its original size
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	if ('' == $req->ref) { $page->do404(); }
	$i = new Images_Image($req->ref);
	if ($i->fileName == '') { $page->do404(); }
			
	//----------------------------------------------------------------------------------------------
	//	return the transform
	//----------------------------------------------------------------------------------------------
	if (file_exists($installPath . $i->fileName)) {
		header('Content-Type: image/jpeg');
		readfile($installPath . $i->fileName);
	} else {
		header('Content-Type: image/jpeg');
		$sync->requestFile($i->fileName);
		readfile($installPath . 'themes/' . $defaultTheme . '/unavailable/loading.jpg');
	}

?>
