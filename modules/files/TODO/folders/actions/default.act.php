<?

//--------------------------------------------------------------------------------------------------
//*	default action for user folders module (ie, no action specified in URL)
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		// no recordAlias/UID given in URL, list all folders
		include $kapenta->installPath . 'modules/folders/actions/tree.act.php';
	} else {
		// recordAlias/UID given in URL, show a specific folder
		include $kapenta->installPath . 'modules/folders/actions/show.act.php';
	}

?>
