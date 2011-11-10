<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//*	send an image at the specified size, managing browser cache, etc
//-------------------------------------------------------------------------------------------------

function imgSend($size) {
	global $kapenta;
	global $req;
	global $page;
	global $sync;

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	
	if ('' == $req->ref) { $page->do404('Image not specified.'); }
	$model = new Images_Image($req->ref);
	if (false == $model->loaded) { $page->do404('Image not found'); }
	if ('' == $model->fileName) { $page->do404('File missing.'); }
	
	$lmDate = date(DATE_RFC1123, $kapenta->strtotime($model->editedOn));
	$eTag = md5($lmDate . $size . $model->hash);
	//echo "eTag: " . $eTag . "<br/>\n";
	//echo 'HTTP_IF_NONE_MATCH: ' . $_SERVER['HTTP_IF_NONE_MATCH'] . "<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	check for If-Modified-Since header
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER))
	   || (true == array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)) ) {

		if ($eTag == $_SERVER['HTTP_IF_NONE_MATCH']) {
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . $eTag . "\"");
			header('Cache-Control: max-age=3600');
	 	    header('HTTP/1.0 304 Not Modified');
			echo ""; flush();				
			die();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	create the thumbnail if it does not exist
	//----------------------------------------------------------------------------------------------
	if (array_key_exists($size, $model->transforms) == false) {
		$exists = $model->loadImage();
		$thumbFile = '';
		if (true == $exists) {
			//-------------------------------------------------------------------------------------
			//	full sized image exists on this peer
			//-------------------------------------------------------------------------------------
			$thumb = 0;
			switch($size) {
				case 'thumb':		$thumb = $model->scaleToBox(100, 100);	break;
				case 'thumbsm':		$thumb = $model->scaleToBox(50, 50);	break;
				case 'thumb90':		$thumb = $model->scaleToBox(90, 90);	break;
				case 'width100':	$thumb = $model->scaleToWidth(100);		break;
				case 'width145':	$thumb = $model->scaleToWidth(145);		break;
				case 'width190':	$thumb = $model->scaleToWidth(190);		break;
				case 'width200':	$thumb = $model->scaleToWidth(200);		break;
				case 'width290':	$thumb = $model->scaleToWidth(290);		break;
				case 'width300':	$thumb = $model->scaleToWidth(300);		break;
				case 'width560':	$thumb = $model->scaleToWidth(560);		break;
				case 'width570':	$thumb = $model->scaleToWidth(570);		break;
				case 'widtheditor':	$thumb = $model->scaleToWidth(530);		break;
				case 'slide':		$thumb = $model->scaleToBox(560, 300);	break;
				default: $page->do404();
			}

			$thumbFile = str_replace('.jpg', '_' . $size . '.jpg', $model->fileName);
			$model->transforms[$size] = $thumbFile;
			//$model->save();
			imagejpeg($thumb, $kapenta->installPath . $thumbFile, 95);
	
		} else {
			//-------------------------------------------------------------------------------------
			//	full sized image does not exist on this peer
			//-------------------------------------------------------------------------------------
			$sync->requestFile($model->fileName);
		}

		if (true == $exists) { 
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . $eTag . "\"");
			header('Cache-Control: max-age=3600');
			readfile($kapenta->installPath . $thumbFile);	

		} else { $page->do302('themes/'. $kapenta->defaultTheme .'/unavailable/'. $size .'.jpg'); }


	} else {
	
		//------------------------------------------------------------------------------------------
		//	output the transform
		//------------------------------------------------------------------------------------------

		if (file_exists($$kapenta->installPath . $model->transforms[$size]) == true) {
			//-------------------------------------------------------------------------------------
			//	file exists on this peer
			//-------------------------------------------------------------------------------------
			header('Content-Type: image/jpeg');
			header('Last-Modified: ' . $lmDate);
			header("ETag: \"" . $eTag . "\"");
			header('Cache-Control: max-age=3600');
			readfile($kapenta->installPath . $model->transforms[$size]);

		} else {
			//-------------------------------------------------------------------------------------
			//	file does not exist on this peer, find it
			//-------------------------------------------------------------------------------------
			//$sync->requestFile($model->transforms[$size]);	//TODO: re-add this with sync object
			$page->do302('themes/'. $kapenta->defaultTheme .'/unavailable/loading'. $size .'.jpg');

		}
	}
}
?>
