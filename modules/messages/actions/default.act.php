<?

	//----------------------------------------------------------------------------------------------
	//	redirect to users inbox unless message UID is supplied
	//----------------------------------------------------------------------------------------------

	if ($request['ref'] == '') {
		include $installPath . 'modules/messages/actions/inbox.act.php';
	} else {
		include $installPath . 'modules/messages/actions/show.act.php';
	}

?>
