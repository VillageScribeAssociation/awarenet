<? 
	if ('' == $req->ref) { // no page specified
		$req->ref = 'Front-Page';
		include $installPath . 'modules/static/actions/show.act.php';
	} else { // page has been specified
		include $installPath . 'modules/static/actions/show.act.php'; 
	}

?>
