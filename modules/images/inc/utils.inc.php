<?

//--------------------------------------------------------------------------------------------------
//*	utility functions for working with images
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	rotate an image by 90 degree steps
//--------------------------------------------------------------------------------------------------
//:	note that the GD imaggerotate function was omitted from several PHP4 and 5 distributions, 
//:	particularly Linux distros, due to an unresolved memory leak, hence this backup.
//credit: beau at dragonflydevelopment dot com
//source: http://www.php.net/manual/en/function.imagerotate.php#95749
//arg: img - GD image handle [int]
//arg: angle - degrees (0, 90, 180, 270) [int]
//returns: a GD image handle to the rotated image, or the original image on failure [int]

function images_rotate($img, $angle) {
	global $session;

	//----------------------------------------------------------------------------------------------
	//	use gd rotation if possible (faster, better)
	//----------------------------------------------------------------------------------------------

	$angle = $angle % 360;
	if (0 !== ($angle % 90)) { 
		$kapenta->session->msg('image_rotate: Angle must be a multiple of 90 degrees.', 'bad');
		return $img;
	}

	if (true == function_exists('imagerotate')) { 
		$newImage = @imagerotate($source, $angle, 0);
		if (false !== $newImage) { return $newImage; }
	}

	$newImg = -1;
	$width = @imagesx($img);			//%	width of image, pixels [int]
	$height = @imagesy($img);			//%	height of image, pixels [int]

	if ((0 == (int)$width) || (0 == (int)$height)) {
		$kapenta->session->msg('Invalid image dimensions.', 'bad');
		return $img;
	}

	switch($angle) {
		case 90:	$newimg= @imagecreatetruecolor($height, $width);	break;
		case 180:	$newimg= @imagecreatetruecolor($width, $height);	break;
		case 270:	$newimg= @imagecreatetruecolor($height, $width);	break;
		case 0: 	return $img;										break;
		case 360:	return $img;										break;

		default:
			$kapenta->session->msg("Images can only be rotated in steps of 90 degrees using this function.");
			return $img;
	}

	if(-1 == $newimg) {
		$kapenta->session->msg('New image canvas could not be created.', 'bad');
		return $img;
	}

	for($i = 0;$i < $width ; $i++) {
		for($j = 0;$j < $height ; $j++) {
			$reference = imagecolorat($img,$i,$j);
			switch($angle) {
				case 90:
					$check = imagesetpixel($newimg, ($height - 1) - $j, $i, $reference);
					if (false == $check) {
						$kapenta->session->msg("Could not set pixel, aborting rotate.");
						return $img;
					}
					break;		//..................................................................

				case 180:
					$check = imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference);
					if (false == $check) {
						$kapenta->session->msg("Could not set pixel, aborting rotate.");
						return $img;
					}
					break;		//..................................................................

				case 270:
					$check = imagesetpixel($newimg, $j, $width - $i, $reference);
					if (false == $check) {
						$kapenta->session->msg("Could not set pixel, aborting rotate.");
						return $img;
					}
					break;		//..................................................................
			}
		}
	}

	return $newimg;
}

?>
