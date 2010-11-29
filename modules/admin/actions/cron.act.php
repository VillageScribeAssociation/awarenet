<?

	require_once($kapenta->installPath . 'core/kcron.class.php');

//--------------------------------------------------------------------------------------------------
//*	this should be called every 10 minutes, it is the entry point of global cron
//--------------------------------------------------------------------------------------------------
//TODO: consider adding a bot user to take credit for changes

	//----------------------------------------------------------------------------------------------
	//	run any outstanding scheduled tasks
	//----------------------------------------------------------------------------------------------

	$cron = new KCron();
	$report = $cron->run();

	$fileName = 'data/log/' . date("y-m-d") . "-cron.log.php";
	$kapenta->filePutContents($fileName, $report, false, false, 'a+');

	//----------------------------------------------------------------------------------------------
	//	display report if user is administrator
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$page->load('modules/admin/actions/cron.page.php');
	$page->blockArgs['report'] = $report;
	$page->render();

?>
