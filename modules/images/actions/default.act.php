<?

//--------------------------------------------------------------------------------------------------
//	display an image, or the entire gallery of all images
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		include $installPath . 'modules/images/actions/showall.act.php';
	} else {
		include $installPath . 'modules/images/actions/show.act.php';
	}

?>
