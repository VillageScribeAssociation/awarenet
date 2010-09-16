<?
	if ('' == $req->ref) { include $installPath . 'modules/users/actions/list.act.php'; }
	else {	include $installPath . 'modules/users/actions/profile.act.php'; }
?>
