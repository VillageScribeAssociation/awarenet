<?

//--------------------------------------------------------------------------------------------------
//*	default action for user galleries module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		// no alias/UID given in URL, list all galleries
		include $kapenta->installPath . 'modules/videos/actions/listallgalleries.act.php';
	} else {
		// alias/UID given in URL, show a specific gallery
		include $kapenta->installPath . 'modules/videos/actions/showgallery.act.php';
	}

?>
