<? 
	if ($request['ref'] == '') { // no page specified
		$request['ref'] = 'Front-Page';
		include $installPath . 'modules/static/actions/show.act.php';
	} else { // page has been specified
		include $installPath . 'modules/static/actions/show.act.php'; 
	}

?>
