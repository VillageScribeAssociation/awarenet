<?

//--------------------------------------------------------------------------------------------------
//	display an file, or the entire gallery of all files
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		include $installPath . 'modules/files/actions/showall.act.php';
	} else {
		include $installPath . 'modules/files/actions/show.act.php';
	}

?>
