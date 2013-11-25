<?

//--------------------------------------------------------------------------------------------------
//*	default action for schools module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		// no recordAlias/UID given in URL, list all schools
		include $kapenta->installPath . 'modules/schools/actions/geographic.act.php';
	} else {
		// recordAlias/UID given in URL, show single school
		include $kapenta->installPath . 'modules/schools/actions/show.act.php';
	}

?>
