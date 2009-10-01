<?

//--------------------------------------------------------------------------------------------------
//	default action for groups module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') {
		// no recordAlias/UID given in URL, list all groups in current users school
		include $installPath . 'modules/groups/actions/list.act.php';
	} else {
		// recordAlias/UID given in URL, show single group
		include $installPath . 'modules/groups/actions/show.act.php';
	}

?>
