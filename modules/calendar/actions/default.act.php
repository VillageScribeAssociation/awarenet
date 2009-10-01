<?

	if ($request['ref'] == '') {
		include $installPath . 'modules/calendar/actions/list.act.php';
	} else {
		include $installPath . 'modules/calendar/actions/show.act.php';
	}

?>