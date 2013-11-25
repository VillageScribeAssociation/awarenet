<?

	require_once($kapenta->installPath . 'modules/chatserver/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test the ten minute cron / session janitorial script
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	echo chatserver_cron_tenmins();

?>
