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
	if ('admin' != $user->role) { $page->do403(); }
	$updateManager = new KUpdateManager();


	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	$report = $updateManager->updateAllLists();
	echo $report;

	//----------------------------------------------------------------------------------------------
	//	previous version of this method, kept for comparison with Kupdatemanager method
	//----------------------------------------------------------------------------------------------

	/*

	$sources = $updateManager->listSources();

	//----------------------------------------------------------------------------------------------
	//	try update all package lists
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Updating all packages</h1>\n";

	foreach($sources as $url) {
		$source = new KSource($url);
		$updateManager->log('<h2>' . $source->url . '</h2>', 'black');
		$check = $source->update();

		if (true == $check) {
			$msg = 'Downloaded new package list from:<br/>' . $url;
			$session->msg($msg, 'ok');
			$updateManager->log($msg, 'green');

			foreach($source->packages as $uid => $meta) {
				//----------------------------------------------------------------------------------
				//	copy metadata to registry
				//----------------------------------------------------------------------------------
				$registry->set("pkg.$uid.name", $meta['name']);
				$registry->set("pkg.$uid.source", $url);

				if ('installed' != $registry->get("pkg.$uid.status")) {
					$registry->set("pkg.$uid.status", 'available');
				}

				//----------------------------------------------------------------------------------
				//	get new manifest for any installed packages which have changed
				//----------------------------------------------------------------------------------
				if (true == $updateManager->isInstalled($uid)) {
					$package = new KPackage($uid);
					if (true == $package->loaded) {
						$registry->set('pkg.' . $package->UID . '.v', $package->version);
						$registry->set('pkg.' . $package->UID . '.r', $package->revision);

						if ($package->revision != $meta['revision']) { 
							//----------------------------------------------------------------------
							//	package has changed, update our manifest
							//----------------------------------------------------------------------
							$check = $package->updateFromRepository();
							if (true == $check) { 
								$msg = ''
								 . 'Package updated: ' . $package->name . '<br/>'
								 . 'Our version: ' . $package->revision . '<br/>'
								 . 'Latest version: ' . $meta['revision'] . '<br/>';

								$session->msg($msg, 'ok'); 
								$updateManager->log($msg, 'green');

								//TODO: check 'updated' field, set registry key if updates available

							} else { 
								$msg = "Could not download manifest: " . $package->$name;
								$session->msg($msg, 'bad');
								$updateManager->log($msg, 'red');
							}

						} else {
							//----------------------------------------------------------------------
							//	package has not changed
							//----------------------------------------------------------------------
							$msg = $package->name . ": package is up to date.";
							$updateManager->log($msg, 'green');
						}
		
						//--------------------------------------------------------------------------
						//	check if this package is dirty (files to be added, removed, updated)
						//--------------------------------------------------------------------------
						$different = $package->getLocalDifferent();
						if (0 == count($different)) {
							$msg = 'Package ' . $package->name . ' is cleanly installed.';
							$updateManager->log($msg, 'green');
						} else {
							$registry->set('pkg.' . $package->UID . '.dirty', 'yes');
							$msg = 'Package ' . $package->name . ' requires updates or cleaning.';
							$updateManager->log($msg, 'red');
							foreach($different as $item) { 
								echo '(' . $item['local'] . ') ' . $item['path'] . "<br/>\n";
							}
						}

					} else {
						//--------------------------------------------------------------------------
						//	package could not be loaded
						//--------------------------------------------------------------------------
						$msg = 'Package could not be loaded: ' . $uid;
						$check = $package->updateFromRepository();
						if (true == $check) { $msg .= "<br>Re-downloaded, please rerun updates."; }
						else { $msg .= "<br/>Could not download manifest."; }
						$updateManager->log($msg, 'red');

					} // end if is loaded
				} // end if isInstalled
			} // end foreach package


		} else {
			$msg = 'Could not downloaded package list from:<br/>' . $url;
			$session->msg($msg, 'bad');
			$updateManager->log($msg, 'red');
		}
	}

	*/

	//----------------------------------------------------------------------------------------------
	//	redirect back to packages console
	//----------------------------------------------------------------------------------------------
	$packagesUrl = $kapenta->serverPath . 'packages/';
	//echo "<script language='Javascript'> window.location ='$packagesUrl'; </script>";
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
