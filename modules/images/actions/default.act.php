<?

//--------------------------------------------------------------------------------------------------
//*	display an image, or the entire gallery of all images
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		//------------------------------------------------------------------------------------------
		//	default action of images module, no arguments
		//------------------------------------------------------------------------------------------
		include $kapenta->installPath . 'modules/images/actions/showall.act.php';

	} else {
		//------------------------------------------------------------------------------------------
		//	return scaled image file, or image page, depending on req args
		//------------------------------------------------------------------------------------------

		if (
			(true == array_key_exists('scale', $kapenta->request->args)) ||
			(true == array_key_exists('s', $kapenta->request->args)) ||
			(true == array_key_exists('p', $kapenta->request->args))
		) {
			include $kapenta->installPath . 'modules/images/actions/scale.act.php';
		}
		else {
			include $kapenta->installPath . 'modules/images/actions/show.act.php';
		}
	}

?>
