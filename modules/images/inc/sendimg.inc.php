<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	send an image at the specified size
//-------------------------------------------------------------------------------------------------

function imgSend($size) {
	global $req, $page, $request, $kapenta, $sync;

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	
	if ('' == $req->ref) { $page->do404(); }
	$i = new Images_Image($req->ref);
	if ('' == $i->fileName) { $page->do404(); }
	
	$lmDate = date(DATE_RFC1123, strtotime($i->createdOn));

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
				case 'width190':	$thumb = $i->scaleToWidth(190);		break;
				case 'width200':	$thumb = $i->scaleToWidth(200);		break;
				case 'width290':	$thumb = $i->scaleToWidth(290);		break;
				case 'width300':	$thumb = $i->scaleToWidth(300);		break;
				case 'width560':	$thumb = $i->scaleToWidth(560);		break;
				case 'width570':	$thumb = $i->scaleToWidth(570);		break;
				case 'widtheditor':	$thumb = $i->scaleToWidth(530);		break;
				case 'slide':		$thumb = $i->scaleToBox(560, 300);	break;
				default: $page->do404();
			}

			$thumbFile = str_replace('.jpg', '_' . $size . '.jpg', $i->fileName);
			$i->transforms[$size] = $thumbFile;
			$i->save();
			imagejpeg($thumb, $kapenta->installPath . $thumbFile, 95);
	
		} else {
			//-------------------------------------------------------------------------------------
			//	full sized image does not exist on this peer
			//-------------------------------------------------------------------------------------
			$sync->requestFile($i->fileName);
		}

		if (true == $exists) { 
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . md5($lmDate . $size) . "\"");
			header('Cache-Control: max-age=3600');
			readfile($kapenta->installPath . $thumbFile);	

		} else { $page->do302('themes/'. $kapenta->defaultTheme .'/unavailable/'. $size .'.jpg'); }


	} else {
	
		//------------------------------------------------------------------------------------------
		//	return the transform
		//------------------------------------------------------------------------------------------

		if (file_exists($$kapenta->installPath . $i->transforms[$size]) == true) {
			//-------------------------------------------------------------------------------------
			//	file exists on this peer
			//-------------------------------------------------------------------------------------
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . md5($lmDate . $size) . "\"");
			header('Cache-Control: max-age=3600');
			readfile($kapenta->installPath . $i->transforms[$size]);

		} else {
			//-------------------------------------------------------------------------------------
			//	file does not exist on this peer, find it
			//-------------------------------------------------------------------------------------
			//$sync->requestFile($i->transforms[$size]);	//TODO: re-add this with sync object
			$page->do302('themes/'. $kapenta->defaultTheme .'/unavailable/loading'. $size .'.jpg');

		}
	}
}
?>
