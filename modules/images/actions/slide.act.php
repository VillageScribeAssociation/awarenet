<?

//--------------------------------------------------------------------------------------------------
//	display an image thumbnail
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the image record
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/images/models/image.mod.php');
	if ($request['ref'] == '') { do404(); }
	$i = new Image($request['ref']);
	if ($i->data['fileName'] == '') { do404(); }
	
	//----------------------------------------------------------------------------------------------
	//	create the thumbnail if it does not exist
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('slide', $i->transforms) == false) {
		$i->loadImage();
		$thumb = $i->scaleToBox(560, 300);
		$thumbFile = str_replace('.jpg', '_slide.jpg', $i->data['fileName']);
		$i->data['transforms'] .= 'slide|' . $thumbFile . "\n";
		$i->expandTransforms();
		$i->save();
		
		imagejpeg($thumb, $installPath . $thumbFile, 95);
		header('Content-Type: image/jpeg');
		readfile($installPath . $thumbFile);

	} else {
	
		//------------------------------------------------------------------------------------------
		//	return the transform
		//------------------------------------------------------------------------------------------
		header('Content-Type: image/jpeg');
		readfile($installPath . $i->transforms['slide']);
	}
?>
