<?

//--------------------------------------------------------------------------------------------------
//	display an image scaled to 570 px wide
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/images/models/image.mod.php');
	if ($request['ref'] == '') { do404(); }
	$i = new Image($request['ref']);
	if ($i->data['fileName'] == '') { do404(); }
	
	$lmDate = date(DATE_RFC1123, strtotime($i->data['createdOn']));

	//----------------------------------------------------------------------------------------------
	//	check for If-Modified-Since header
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER) == true)
	   || (array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER) == true) ) {
		header('Last-Modified: ' . $lmDate);
		header("ETag: \"" . md5($lmDate) . "\"");
		header('Cache-Control: max-age=13600');
 	    header('HTTP/1.0 304 Not Modified');
		echo ""; flush();				
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	create the thumbnail if it does not exist
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('width570', $i->transforms) == false) {
		$i->loadImage();
		$thumb = $i->scaleToWidth(570);
		$thumbFile = str_replace('.jpg', '_width570.jpg', $i->data['fileName']);
		$i->data['transforms'] .= 'width570|' . $thumbFile . "\n";
		$i->expandTransforms();
		$i->save();
		
		imagejpeg($thumb, $installPath . $thumbFile, 95);
		header('Content-Type: image/jpeg');
		header('Last-Modified: ' . $lmDate);
		header("ETag: \"" . md5($lmDate) . "\"");
		header('Cache-Control: max-age=13600');
		readfile($installPath . $thumbFile);

	} else {
	
		//------------------------------------------------------------------------------------------
		//	return the transform
		//------------------------------------------------------------------------------------------
		header('Content-Type: image/jpeg');
		header('Last-Modified: ' . $lmDate);
		header("ETag: \"" . md5($lmDate) . "\"");
		header('Cache-Control: max-age=13600');
		readfile($installPath . $i->transforms['width570']);
	}
?>
