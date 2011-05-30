<?

//--------------------------------------------------------------------------------------------------
//*	default action of calendar module
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) {
		include $kapenta->installPath . 'modules/calendar/actions/list.act.php';
	} else {
		include $kapenta->installPath . 'modules/calendar/actions/show.act.php';
	}

?>
