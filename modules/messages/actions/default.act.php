<?

//--------------------------------------------------------------------------------------------------
//*	redirect to users inbox unless message UID is supplied
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) {
		include $kapenta->installPath . 'modules/messages/actions/inbox.act.php';
	} else {
		include $kapenta->installPath . 'modules/messages/actions/show.act.php';
	}

?>
