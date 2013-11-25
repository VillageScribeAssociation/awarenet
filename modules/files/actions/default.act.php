<?

//--------------------------------------------------------------------------------------------------
//*	display a file, or list of files
//--------------------------------------------------------------------------------------------------
//TODO: create alternate default views, switch with registry

	//temporarily disabled:
	$page->do404();

	if ('' == $kapenta->request->ref) {
		include $kapenta->installPath . 'modules/files/actions/showall.act.php';
	} else {
		include $kapenta->installPath . 'modules/files/actions/show.act.php';
	}

?>
