<?

//--------------------------------------------------------------------------------------------------
//*	default action for code module is to show a package, or list of packages if nor ref given
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		include $kapenta->installPath . 'modules/code/actions/list.act.php';
	} else {
		include $kapenta->installPath . 'modules/code/actions/show.act.php';
	}

?>
