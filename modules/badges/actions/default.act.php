<?

//--------------------------------------------------------------------------------------------------
//*	default action for badges module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		// no recordAlias/UID given in URL, list all groups in current users school
		include $kapenta->installPath . 'modules/badges/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show single group
		include $kapenta->installPath . 'modules/badges/actions/show.act.php';
	}

?>
