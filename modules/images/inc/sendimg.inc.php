<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	send an image at the specified size
//-------------------------------------------------------------------------------------------------

function imgSend($size) {
	global $request;
	global $installPath;
	global $defaultTheme;

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	
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
		header("ETag: \"" . md5($lmDate . $size) . "\"");
		header('Cache-Control: max-age=3600');
 	    header('HTTP/1.0 304 Not Modified');
		echo ""; flush();				
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	create the thumbnail if it does not exist
	//----------------------------------------------------------------------------------------------
	if (array_key_exists($size, $i->transforms) == false) {
		$exists = $i->loadImage();
		$thumbFile = '';
		if (true == $exists) {
			//-------------------------------------------------------------------------------------
			//	full sized image exists on this peer
			//-------------------------------------------------------------------------------------
			$thumb = 0;
			switch($size) {
				case 'thumb':		$thumb = $i->scaleToBox(100, 100);	break;
				case 'thumbsm':		$thumb = $i->scaleToBox(50, 50);	break;
				case 'thumb90':		$thumb = $i->scaleToBox(90, 90);	break;
				case 'width100':	$thumb = $i->scaleToWidth(100);		break;
				case 'width145':	$thumb = $i->scaleToWidth(145);		break;
				case 'width200':	$thumb = $i->scaleToWidth(200);		break;
				case 'width290':	$thumb = $i->scaleToWidth(290);		break;
				case 'width300':	$thumb = $i->scaleToWidth(300);		break;
				case 'width560':	$thumb = $i->scaleToWidth(560);		break;
				case 'width570':	$thumb = $i->scaleToWidth(570);		break;
				case 'widtheditor':	$thumb = $i->scaleToWidth(530);		break;
				case 'slide':		$thumb = $i->scaleToBox(560, 300);	break;
				default: do404();
			}

			$thumbFile = str_replace('.jpg', '_' . $size . '.jpg', $i->data['fileName']);
			$i->data['transforms'] .= $size . '|' . $thumbFile . "\n";
			$i->expandTransforms();
			$i->save();
			imagejpeg($thumb, $installPath . $thumbFile, 95);
	
		} else {
			//-------------------------------------------------------------------------------------
			//	full sized image does not exist on this peer
			//-------------------------------------------------------------------------------------
			syncRequestFile($i->data['fileName']);
		}

		if (true == $exists) { 
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . md5($lmDate . $size) . "\"");
			header('Cache-Control: max-age=3600');
			readfile($installPath . $thumbFile);	
		} else { do302('themes/' . $defaultTheme . '/unavailable/' . $size . '.jpg'); }


	} else {
	
		//------------------------------------------------------------------------------------------
		//	return the transform
		//------------------------------------------------------------------------------------------

		if (file_exists($installPath . $i->transforms[$size]) == true) {
			//-------------------------------------------------------------------------------------
			//	file exists on this peer
			//-------------------------------------------------------------------------------------
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . md5($lmDate . $size) . "\"");
			header('Cache-Control: max-age=3600');
			readfile($installPath . $i->transforms[$size]);

		} else {
			//-------------------------------------------------------------------------------------
			//	file does not exist on this peer, find it
			//-------------------------------------------------------------------------------------
			syncRequestFile($i->transforms[$size]);
			do302('themes/' . $defaultTheme . '/unavailable/loading' . $size . '.jpg');

		}
	}
}
?>
