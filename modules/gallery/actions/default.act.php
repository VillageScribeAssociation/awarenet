<?

//--------------------------------------------------------------------------------------------------
//	default action for user galleries module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		// no recordAlias/UID given in URL, list all galleries
		include $installPath . 'modules/gallery/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show a specific gallery
		include $installPath . 'modules/gallery/actions/show.act.php';
	}

?>
