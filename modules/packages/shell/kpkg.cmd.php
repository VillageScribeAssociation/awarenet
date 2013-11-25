<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//|	lists packages installed on this kapenta instance
//--------------------------------------------------------------------------------------------------

function packages_WebShell_kpkg($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $kapenta;
	global $theme;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('-c', $args)) { $mode = 'clean'; }
	if (true == in_array('--clean', $args)) { $mode = 'clean'; }
	if (true == in_array('-d', $args)) { $mode = 'delete'; }
	if (true == in_array('--delete', $args)) { $mode = 'delete'; }
	if (true == in_array('--fix', $args)) { $mode = 'fix'; }
	if (true == in_array('-f', $args)) { $mode = 'fix'; }
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }
	if (true == in_array('--installed', $args)) { $mode = 'installed'; }
	if (true == in_array('-i', $args)) { $mode = 'installed'; }
	if (true == in_array('--checkout', $args)) { $mode = 'checkout'; }
	if (true == in_array('-k', $args)) { $mode = 'checkout'; }	
	if (true == in_array('--sources', $args)) { $mode = 'sources'; }
	if (true == in_array('-s', $args)) { $mode = 'sources'; }
	if (true == in_array('--update', $args)) { $mode = 'update'; }
	if (true == in_array('-u', $args)) { $mode = 'update'; }

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	check if a package name was given
	//----------------------------------------------------------------------------------------------

	//TODO: this

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {

		case 'clean':
			//--------------------------------------------------------------------------------------
			//	remove extraneous junk from registry, also clears stored usernames and passwords
			//--------------------------------------------------------------------------------------
			$keys = $kapenta->registry->search('pkg', 'pkg');
			foreach($keys as $key => $val) {
				$del = false;
				if (false !== strpos($key, '.user')) { $del = true; }
				if (false !== strpos($key, '.pass')) { $del = true; }
				if (false !== strpos($key, '.versio')) { $del = true; }
				if (false !== strpos($key, '.revisio')) { $del = true; }


				if (true == $del) {
					$html .= "Removing... <span class='ajaxwarn'>$key</span><br/>";
					$kapenta->registry->delete($key);
				}

				if ((false !== strpos($key, '.status')) && ('dirty' === $val)) {
					$kapenta->registry->set($key, 'installed');
				}

			}

			break;		//..........................................................................

		case 'checkout':
			//--------------------------------------------------------------------------------------
			//	make a list of all packages
			//--------------------------------------------------------------------------------------
			$updateManager = new KUpdateManager();
			$installed = $updateManager->listAllPackages();		//%	[array:string]

			foreach($installed as $UID => $pkg) {
				$package = new KPackage($UID);

				if (
					(false == $package->loaded) ||
					(false == $updateManager->isInstalled($UID))
				) {
					$html .= "Skipping package $UID.<br/>\n";
		
				} else {
					//-----------------------------------------------------------------------------
					//	get all the files
					//-----------------------------------------------------------------------------
					$html .= "Updating package: " . $package->name . "<br/>\n";
		
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
								$html .= 'Updated: ' . $pf['path'] . "<br/>\n";
								$changeCount++;
							} else {
								$html .= 'Could not update: ' . $pf['path'] . "<br/>\n";
								$toRetry[] = $pf;
							}
						} else {
							$ignoreCount++;
						}
					}

					$html .= ''
					 . "Files: $changeCount updated, $ignoreCount unchanged, "
					 . count($toRetry) . " failed.<br/>\n";
		
					//--------------------------------------------------------------------------------------
					//	retry any which failed
					//--------------------------------------------------------------------------------------
					//TODO: this

				} // end if loaded
			} // end foreach package

			break;		//..........................................................................

		case 'delete':
			//--------------------------------------------------------------------------------------
			//	delete cached manifests and package lists
			//--------------------------------------------------------------------------------------
			$files = $kapenta->fileList('data/packages/', '.xml.php');
			
			foreach($files as $file) {
				$check = $kapenta->fileDelete($file);
				$status = "<span class='ajaxmsg'>OK</span>";
				if (false == $check) { $status = "<span class='ajaxwarn'>could not delete</span>"; }
				$html .= "Removing: $file $status<br/>\n";
			}

			break;		//..........................................................................

		case 'list':
			//--------------------------------------------------------------------------------------
			//	list all packages available from all sources
			//--------------------------------------------------------------------------------------
			$updateManager = new KUpdateManager();
			$sources = $updateManager->listSources();

			foreach($sources as $sourceUrl) {
				$html .= "<b>$sourceUrl</b><br/>";
		
				$source = new KSource($sourceUrl);
				if (false == $source->loaded) {
					$html .= "<span class='ajaxerror'>Could not load package list.</span><br/>\n";
				} else {
					$pkgs = $source->listPackages();
					foreach($pkgs as $pkgUID => $pkgName) {
						$installed = "<span class='ajaxwarn'>not installed</span>";
						if ('installed' == $kapenta->registry->get('pkg.' . $pkgUID . '.status')) { 
							$installed = "<span class='ajaxmsg'>installed</span>";
						}
						$html .= "$pkgName ($pkgUID) $installed<br/>\n";
					}
				}

			}

			break;	//..............................................................................

		case 'fix':
			//TODO: this should load and check manifests, and run the install status function

			$kapenta->registry->load('pkg');
			$packages = array();			//%	all packages in registry [array:dict]
			$sources = array();				//%	all package sources [array:string]
			$installed = array();			//%	UIDs of installed packages [array:string]
			$table = array();				//%	[array:string]
	
			//--------------------------------------------------------------------------------------
			//	get packages fron registry
			//--------------------------------------------------------------------------------------
			foreach($registry->keys as $key => $value64) {
				if ('pkg' == $kapenta->registry->getPrefix($key)) {
					$parts = explode('.', $key);
					if (3 == count($parts)) {
						if (false == array_key_exists($parts[1], $packages)) {
							$packages[$parts[1]] = array();
						}
						$packages[$parts[1]][$parts[2]] = $kapenta->registry->get($key);
					}
				}
			}

			//--------------------------------------------------------------------------------------
			//	make packages list and set 'dirty' to force check
			//--------------------------------------------------------------------------------------
			foreach($packages as $UID => $pkg) {
				if (
					(true == array_key_exists('status', $pkg)) && 
					(true == array_key_exists('source', $pkg)) && 
					('installed' == $pkg['status']) &&
					('' != $pkg['source'])
				) {
										
					$kapenta->registry->set('pkg.' . $UID . '.dirty', 'yes');
					$installed[] = $UID;
					if (false == in_array($pkg['source'], $sources)) { $sources[] = $pkg['source'];	}
					$html .= "package: $UID (installed from " . $pkg['source'] . ")<br/>";
				}

				if ('dirty' == $kapenta->registry->get('pkg.' . $UID . '.status')) {
					$kapenta->registry->set('pkg.' . $UID . '.status', 'installed');
				}
			}

			$kapenta->registry->set('kapenta.sources.list', implode('|', $sources));
			break;	//..............................................................................


		case 'help':
			$html = packages_WebShell_kpkg_help();
			break;			

		case 'installed':
			//--------------------------------------------------------------------------------------
			//	list installed packages
			//--------------------------------------------------------------------------------------
			$html = $theme->expandBlocks('[[:packages::installedpackages:]]');
			break;	//..............................................................................

		case 'sources':
			//--------------------------------------------------------------------------------------
			//	display list of software sources
			//--------------------------------------------------------------------------------------
		
			$updateManager = new KUpdateManager();
			$sources = $updateManager->listSources();

			foreach($sources as $sourceUrl) {
				$html .= "$sourceUrl<br/>\n";
			}

			break;	//..............................................................................

		case 'show':
			$html .= "TODO: this";
			break;	//..............................................................................

		case 'update':
			$updateManager = new KUpdateManager();
			$updateManager->updateAllLists();
			break;	//..............................................................................

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.time command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function packages_WebShell_kpkg_help($short = false) {
	if (true == $short) { return "List packages installed on this system."; }

	$html = "
	<b>usage: pakages.kpkg [-s|--show] [UID|Name]</b><br/>
	Displays contents of an installed kapenta package.
	<br/>
	<b>[--clean|-c]</b><br/>
	Clean registry of dev options and other junk.<br/>
	<br/>
	<b>[--delete|-d]</b><br/>
	Delete cached package manifests, will force re-download on next update.<br/>
	<br/>
	<b>[--fix|-f]</b><br/>
	Attempt to repair broken metadata and registry entries.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--installed|-i]</b><br/>
	List installed packages.<br/>
	<br/>
	<b>[--sources|-s]</b><br/>
	Show software source(s) used by this package manager.<br/>
	<br/>
	<b>[--update|-u]</b><br/>
	Update package lists and manifests from repository.<br/>
	<br/>
	";

	return $html;
}


?>
