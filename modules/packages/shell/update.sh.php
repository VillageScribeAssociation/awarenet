<?

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell script to update package lists
//--------------------------------------------------------------------------------------------------

	echo "Updating all package lists... please wait.\n";

	$updateManager = new KUpdateManager();
	$updateManager->updateAllLists();

?>
