<?

//--------------------------------------------------------------------------------------------------
//*	display an image, or the entire gallery of all images
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		include $kapenta->installPath . 'modules/images/actions/showall.act.php';
	} else {
		include $kapenta->installPath . 'modules/images/actions/show.act.php';
	}

?>
