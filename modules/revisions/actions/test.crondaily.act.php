<?

	require_once($kapenta->installPath . 'modules/revisions/inc/cron.inc.php');

//--------------------------------------------------------------------------------------------------
//*	test/development action to test sceduled undeletion of shared items
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	
	$report = revisions_cron_daily();
	
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo $report;
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
