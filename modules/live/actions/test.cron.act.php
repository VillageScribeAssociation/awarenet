<?

	require_once($kapenta->installPath . 'modules/live/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test daily cron / cleanup
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	echo live_cron_daily();

?>
