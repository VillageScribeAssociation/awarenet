<?

//--------------------------------------------------------------------------------------------------
//	default action for announcements module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') {
		// no recordAlias/UID given in URL, list all announcements in current users school
		include $installPath . 'modules/announcements/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show single group
		include $installPath . 'modules/announcements/actions/show.act.php';
	}

?>
