<?

//--------------------------------------------------------------------------------------------------
//	default action for user galleries module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') {
		// no recordAlias/UID given in URL, list all forums
		include $installPath . 'modules/forums/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show a specific forum
		include $installPath . 'modules/forums/actions/show.act.php';
	}

?>
