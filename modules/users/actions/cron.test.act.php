<?

	require_once($kapenta->installPath . 'modules/users/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test suer cron scripts
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$report = users_cron_tenmins();
	echo $report;

?>
