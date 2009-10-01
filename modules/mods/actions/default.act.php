<? 
	if ($request['ref'] == '') {
		include $installPath . 'modules/mods/actions/list.act.php';
	} else {
		include $installPath . 'modules/mods/actions/manage.act.php'; 
	}
?>
