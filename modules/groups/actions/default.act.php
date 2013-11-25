<?

//--------------------------------------------------------------------------------------------------
//*	default action for groups module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		// no alias/UID given in URL, list all groups in current users school
		include $kapenta->installPath . 'modules/groups/actions/list.act.php';
	} else {
		// alias/UID given in URL, show single group
		include $kapenta->installPath . 'modules/groups/actions/show.act.php';
	}

?>
