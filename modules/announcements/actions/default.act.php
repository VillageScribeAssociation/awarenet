<?

//--------------------------------------------------------------------------------------------------
//*	default action for announcements module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		// no recordAlias/UID given in URL, list all announcements in current users school
		include $kapenta->installPath . 'modules/announcements/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show single group
		include $kapenta->installPath . 'modules/announcements/actions/show.act.php';
	}

?>
