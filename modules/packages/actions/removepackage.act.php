<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	remvoe a package from the update manager
//--------------------------------------------------------------------------------------------------
//+	Note that this will not remove files or data already installed on this system.
//+
//post: action - set to removePackage [string]
//post: UID - UID of package on repository [string]

	//----------------------------------------------------------------------------------------------
	//	check post vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$UID = '';				//%	UID of package [string]

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not given.', true); }
	if ('removePackage' != $_POST['action']) { $page->do404('Action not recognized.'); }

	if (true == array_key_exists('UID', $_POST)) { $UID = $_POST['UID']; }
	if ('' == trim($UID)) { $page->do404('UID not given.'); }

	$um = new KUpdateManager();
	$package = new KPackage($UID);

	//----------------------------------------------------------------------------------------------
	//	option to delete the package manifest
	//----------------------------------------------------------------------------------------------
	/*	TODO
	if (true == $kapenta->fileExists($package->fileName)) {
		$check = @unlink($kapenta->installPath . $package->fileName);
		if (true == $check) {
			$session->msg("Deleted package manifest: " . $package->fileName, 'ok');
		} else {
			$session->msg("Could not delete package manifest: " . $package->fileName, 'bad');
		}
	} else {
		$session->msg("No manifest do remove: " . $package->fileName);
	}
	*/

	//----------------------------------------------------------------------------------------------
	//	option to uninstall the module
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	option to delete all data and dependant objects
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	set status to 'available' and remove from list of installed packages
	//----------------------------------------------------------------------------------------------
	
	$um->setPackageField($package->UID, 'status', 'available');

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	$page->do302('packages/');

?>
