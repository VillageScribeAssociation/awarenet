<?

//--------------------------------------------------------------------------------------------------
//*	display a file, or list of files
//--------------------------------------------------------------------------------------------------
//TODO: create alternate default views, switch with registry

	if ('' == $req->ref) {
		include $kapenta->installPath . 'modules/files/actions/showall.act.php';
	} else {
		include $kapenta->installPath . 'modules/files/actions/show.act.php';
	}

?>
