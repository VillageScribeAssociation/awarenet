<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	update local files from repository from all packages
//--------------------------------------------------------------------------------------------------
//postarg: packageUID - UID of an installed package [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make a list of all packages
	//----------------------------------------------------------------------------------------------
	$updateManager = new KUpdateManager();
	$installed = $updateManager->listAllPackages();		//%	[array:string]

	foreach($installed as $UID => $pkg) {
		$package = new KPackage($UID);

		if (
			(false == $package->loaded) ||
			(false == $updateManager->isInstalled($UID))
		) {
			$kapenta->session->msg("Skipping package $UID.", 'bad');

		} else {
			//--------------------------------------------------------------------------------------
			//	get all the files
			//--------------------------------------------------------------------------------------
			$kapenta->session->msg("Updating package: " . $package->name);

			$changeCount = 0;
			$ignoreCount = 0;
			$toRetry = array();

			foreach($package->files as $pf) {
				$download = false;
				if (false == $kapenta->fs->exists($pf['path'])) { $download = true; }
				else {
					if ($pf['hash'] != $package->getFileHash($pf['path'])) { $download = true; }
				}

				if (true == $download) { 
					$check = $package->updateFile($pf['uid']);
					if (true == $check) {
						$kapenta->session->msg('Updated: ' . $pf['path'], 'ok');
						$changeCount++;
					} else {
						$kapenta->session->msg('Could not update: ' . $pf['path'], 'bad');
						$toRetry[] = $pf;
					}
				} else {
					$ignoreCount++;
				}
			}

			$msg = ''
			 . "Files: $changeCount updated, $ignoreCount unchanged, "
			 . count($toRetry) . " failed.";
			$kapenta->session->msg($msg, 'ok');

			//--------------------------------------------------------------------------------------
			//	retry any which failed
			//--------------------------------------------------------------------------------------
			//TODO: this

		} // end if loaded
	} // end foreach package

	//----------------------------------------------------------------------------------------------
	//	return to package listing
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('packages/');

?>
