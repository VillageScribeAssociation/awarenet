<?

	require_once($kapenta->installPath . 'modules/live/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test daily cron / cleanup
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	echo live_cron_daily();

?>
