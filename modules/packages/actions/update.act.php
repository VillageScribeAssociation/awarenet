<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');

//--------------------------------------------------------------------------------------------------
//*	refresh package lists
//--------------------------------------------------------------------------------------------------
//+	This action refreshes package lists from all registered sources, regardless of how recent
//+	our version is.  Equivalent to update-all in apt.

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	$updateManager = new KUpdateManager();


	echo $theme->expandBlocks('[[:theme::ifscrollheader::title=Updating all packages:]]'); flush();
	$report = $updateManager->updateAllLists();
	echo $report;

	//----------------------------------------------------------------------------------------------
	//	redirect back to packages console
	//----------------------------------------------------------------------------------------------
	$packagesUrl = $kapenta->serverPath . 'packages/';

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
