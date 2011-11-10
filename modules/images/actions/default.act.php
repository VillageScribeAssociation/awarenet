<?

//--------------------------------------------------------------------------------------------------
//*	display an image, or the entire gallery of all images
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		//------------------------------------------------------------------------------------------
		//	default action of images module, no arguments
		//------------------------------------------------------------------------------------------
		include $kapenta->installPath . 'modules/images/actions/showall.act.php';

	} else {
		//------------------------------------------------------------------------------------------
		//	return scaled image file, or image page, depending on req args
		//------------------------------------------------------------------------------------------

		if (
			(true == array_key_exists('scale', $req->args)) ||
			(true == array_key_exists('s', $req->args)) ||
			(true == array_key_exists('p', $req->args))
		) {
			include $kapenta->installPath . 'modules/images/actions/scale.act.php';
		}
		else {
			include $kapenta->installPath . 'modules/images/actions/show.act.php';
		}
	}

?>
