<?

//--------------------------------------------------------------------------------------------------
//	show an abuse report is specified, list them if not
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		include ($kapenta->installPath . 'modules/abuse/actions/list.act.php');
	} else {
		include ($kapenta->installPath . 'modules/abuse/actions/show.act.php');
	}

?>
