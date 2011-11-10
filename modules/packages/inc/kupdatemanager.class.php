<?

	require_once('modules/packages/inc/ksource.class.php');
	require_once('modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//*	object for managing software sources and installed packages
//--------------------------------------------------------------------------------------------------
//+	Note that this object should only be created once kapenta has been initialized
//+	
//+	Software sources should be refreshed daily by the cron and lists of packages updated.
//+	Installed packages are then compared based on the 'updated' date, and any new manifests for
//+	are downloaded.  If there are updates a registry key called 'kapenta.updates.available' is 
//+	set to 'yes' and 'kapenta.updates.count' to the number of packages requiring attention.
//+
//+	Packages status is recorded in the 'pkg' section of the registry, each should have these keys:
//+
//+		pkg.1234567890.source	- repository URL [string]
//+		pkg.1234567890.name		- package name [string]
//+		pkg.1234567890.status	- (available|installed|removed|nosource) [string]
//+	
//+	Optional keys, set as needed:
//+	
//+		pkg.1234567890.dirty	- whether the package is dirty or not (yes|no) [string]
//+		pkg.1234567890.user		- repository username for commit [string]
//+		pkg.1234567890.pass		- repository password for commit [string]
//+
//+	Where 1234567890 is the UID of the package.

class KUpdateManager {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $sources;				//_	list of software sources [array:string]
	var $packages;				//_	set of all available packages [array:array:string]
	var $installed;				//_	set of installed packages (UID only) [array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KUpdateManager() {
		global $registry;

		$this->sources = $this->listSources();				// get list of sources from registry
		$this->packages = $this->listAllPackages();			// make list of all packages
		$this->installed = $this->getPackageList();			// get list of installed packages 
	}

	//==============================================================================================
	//	sources recorded in registry
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	gets the list of software sources from the registry
	//----------------------------------------------------------------------------------------------
	//returns: array of repository URLs [array:string]

	function listSources() {
		global $registry;
		$sources = array();									//%	return value [array]
		$uids = $registry->get('kapenta.sources.list');		//%	[string]
		$ary = explode('|', $uids);							//%	[array:string]
		foreach($ary as $source) {
			if ('' != trim($source)) { $sources[] = $source; }
		}
		return $sources;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a source is already added
	//----------------------------------------------------------------------------------------------
	//returns: true on if exists, false if not [bool]

	function hasSource($url) {
		foreach($this->sources as $source) {
			if (strtolower($url) == strtolower($source)) { return true; }
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a new software source
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of a kapenta repository [string]
	//returns: true on success, false on failure [bool]

	function addSource($url) {
		global $registry;
		if ('' == trim($url)) { return false; }
		if (true == $this->hasSource($url)) { return false; }
		$this->sources[] = $url;
		$ser = implode('|', $this->sources);				//%	serialized array [string]
		$registry->set('kapenta.sources.list', $ser);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a software source from the list
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of a kapenta repository [string]
	//returns: true on success, false on failure [bool]

	function removeSource($url) {
		global $registry;

		$found = false;				//%	return value [bool]

		//------------------------------------------------------------------------------------------
		//	mark all installed packages from this source as 'nosource'
		//------------------------------------------------------------------------------------------
		$packages = $this->listInstalledPackages();
		foreach($packages as $pUID) {
			if ($registry->get("pkg.$pUID.source") == $url) {
				$registry->set("pkg.$pUID.status", 'nosource');
			}
		}

		//------------------------------------------------------------------------------------------
		//	remove from 'kapenta.sources.list' registry key
		//------------------------------------------------------------------------------------------
		$this->sources = $this->listSources();
		$newSources = array();
		foreach($this->sources as $source) {
			if ($url == $source) { $found = true; }
			else { $newSources[] = $source; }
		}
		$this->sources = $newSources;
		$ser = implode('|', $this->sources);				//%	serialized array [string]
		$registry->set('kapenta.sources.list', $ser);
		return $found;
	}

	//==============================================================================================
	//	packages recorded in source lists
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	get details of an individual package
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of an installed package [string]
	//returns: array of package metadata, or empty array on failure [array:string]

	function getPackageDetails($UID) {
		global $registry;
		$meta = array();				//%	return value [array]

		if (false == array_key_exists($UID, $this->packages)) { return $meta; }	
		$meta = $this->packages[$UID];

		$source = new KSource($meta['source']);
		$more = $source->getPackageDetails($UID);
		foreach($more as $key => $value) { $meta[$key] = $value; }

		return $meta;
	}

	//==============================================================================================
	//	packages recorded in registry
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	get set of packages known to this system (metadata array)
	//----------------------------------------------------------------------------------------------
	//returns: array of packageUID => metadata [array:dict]

	function listAllPackages() {
		global $registry;
		$packages = array();			//%	all package details [dict:dict]

		$registry->load('pkg');	
		foreach($registry->keys as $key => $val64) {
			$parts = explode('.', $key);
			if (('pkg' == $registry->getPrefix($key)) && (3 == count($parts))) {
				$UID = $parts[1];					//% package UID [string]
				$field = $parts[2];					//% package field [string]
				if (false == array_key_exists($UID, $packages)) { 
					$packages[$UID] = array('source' => '', 'status' => '');
				}
				$packages[$UID][$field] = $registry->get($key);
			}
		}


		foreach($packages as $UID => $pkg) {
			$packages[$UID]['UID'] = $UID;
			$packages[$UID]['manifestFile'] = 'data/packages/' . $UID . '.xml.php';
			if (true == array_key_exists('v', $packages[$UID])) { 
				$packages[$UID]['version'] = $packages[$UID]['v'];
			}
			if (true == array_key_exists('r', $packages[$UID])) { 
				$packages[$UID]['revision'] = $packages[$UID]['r'];
			}
		}

		return $packages;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	get set of packages installed on this system (UIDs only)
	//----------------------------------------------------------------------------------------------
	//returns: array of packageUID => metadata [array:array:string]

	function listInstalledPackages() {
		global $registry;
		$packages = $this->listAllPackages();
		$installed = array();

		foreach($packages as $UID => $pkg) {
			if (true == array_key_exists('status', $pkg)) {
				if ('installed' == $pkg['status']) { $installed[$UID] = $pkg; }
			} else {
				$this->setPackageField($UID, 'status', 'available');
			}
		}

		return $installed;
	}


	//----------------------------------------------------------------------------------------------
	//.	get set of installed packages as array of UIDs
	//----------------------------------------------------------------------------------------------
	//returns: array of package UIDs [array:string]

	function getPackageList() {
		global $registry;
		$uids = array();
		$installed = $this->listInstalledPackages();
		foreach($installed as $UID => $package) {
			if ('installed' == $package['status']) { $uids[] = $UID; }
		}

		return $uids;
	}

	//----------------------------------------------------------------------------------------------
	//.	download new package lists from all repositories and update and manfests which have changed
	//----------------------------------------------------------------------------------------------
	//TODO: break this up into multiple functions

	function updateAllLists() {
		global $registry;
		global $session;

		$report = '';							//%	return value [string]
		$sources = $this->listSources();		//%	software source URLs [array:string]

		//------------------------------------------------------------------------------------------
		//	try update all package lists
		//------------------------------------------------------------------------------------------

		echo "<h1>Updating all packages</h1>\n";

		foreach($sources as $url) {
			$source = new KSource($url);
			$this->log('<h2>' . $source->url . '</h2>', 'black');
			$check = $source->update();

			if (true == $check) {
				$msg = 'Downloaded new package list from:<br/>' . $url;
				$session->msg($msg, 'ok');
				$this->log($msg, 'green');

				foreach($source->packages as $uid => $meta) {
					//------------------------------------------------------------------------------
					//	copy metadata to registry
					//------------------------------------------------------------------------------
					$registry->set("pkg.$uid.name", $meta['name']);
					$registry->set("pkg.$uid.source", $url);

					if ('installed' != $registry->get("pkg.$uid.status")) {
						$registry->set("pkg.$uid.status", 'available');
					}

					//------------------------------------------------------------------------------
					//	get new manifest for any installed packages which have changed
					//------------------------------------------------------------------------------
					if (true == $this->isInstalled($uid)) {
						$package = new KPackage($uid);
						if (true == $package->loaded) {
							$registry->set('pkg.' . $package->UID . '.v', $package->version);
							$registry->set('pkg.' . $package->UID . '.r', $package->revision);

							if ($package->revision != $meta['revision']) { 
								//------------------------------------------------------------------
								//	package has changed, update our manifest
								//------------------------------------------------------------------
								$check = $package->updateFromRepository();
								if (true == $check) { 
									$msg = ''
									 . 'Package updated: ' . $package->name . '<br/>'
									 . 'Our version: ' . $package->revision . '<br/>'
									 . 'Latest version: ' . $meta['revision'] . '<br/>';
	
									$session->msg($msg, 'ok'); 
									$this->log($msg, 'green');

									//TODO: check 'updated' field, 
									// set registry key if updates available

								} else { 
									$msg = "Could not download manifest: " . $package->$name;
									$session->msg($msg, 'bad');
									$this->log($msg, 'red');
								}

							} else {
								//------------------------------------------------------------------
								//	package has not changed
								//------------------------------------------------------------------
								$msg = $package->name . ": package is up to date.";
								$this->log($msg, 'green');
							}
		
							//----------------------------------------------------------------------
							//	check if this package is dirty (files to be added, removed, updated)
							//----------------------------------------------------------------------
							$different = $package->getLocalDifferent();
							if (0 == count($different)) {
								$msg = 'Package ' . $package->name . ' is cleanly installed.';
								$this->log($msg, 'green');
							} else {
								$registry->set('pkg.' . $package->UID . '.dirty', 'yes');
								$msg = 'Package '. $package->name .' requires updates or cleaning.';
								$this->log($msg, 'red');
								foreach($different as $item) { 
									echo '(' . $item['local'] . ') ' . $item['path'] . "<br/>\n";
								}
							}

						} else {
							//----------------------------------------------------------------------
							//	package could not be loaded
							//----------------------------------------------------------------------
							$msg = 'Package could not be loaded: ' . $uid;
							$check = $package->updateFromRepository();
							if (true == $check) {
								$msg .= "<br>Re-downloaded, please rerun updates.";
							} else {
								$msg .= "<br/>Could not download manifest.";
							}
							$this->log($msg, 'red');

						} // end if is loaded
					} // end if isInstalled
				} // end foreach package


			} else {
				$msg = 'Could not downloaded package list from:<br/>' . $url;
				$session->msg($msg, 'bad');
				$this->log($msg, 'red');
			}
		} // end foreach source
		$this->log('<h3>Done.</h3>', 'black');
	}

	//----------------------------------------------------------------------------------------------
	//.	update kapenta.packages.list
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: does nothing

	function updatePackageList() {}

	//----------------------------------------------------------------------------------------------
	//.	add an installed package
	//----------------------------------------------------------------------------------------------
	//arg: packageUID - UID of an installed package [string]
	//returns: true on success, false on failure [bool]

	function setPackageField($packageUID, $field, $value) {
		global $registry;

		$key = 'pkg.' . $packageUID . '.' . $field;
		$old = $registry->get($key);
		if ($value != $old) { $registry->set($key, $value); }
		if ('status' == $field) { $this->updatePackageList(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a package is installed on this system
	//----------------------------------------------------------------------------------------------
	//arg: packageUID - UID of a kapenta package [string]
	//returns: True if package is configured on this system, false if not [bool]

	function isInstalled($packageUID) {
		if (true == in_array($packageUID, $this->installed)) { return true; }
		return false;
	}

	//==============================================================================================
	//	ADDING AND REMOVING PACKAGES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	add a package to the registry
	//----------------------------------------------------------------------------------------------
	//;	note that package manifest should already be present on the system
	//arg: source - URL of a kapenta repository [string]
	//arg: packageUID - UID of a software package [string]
	//opt: username - repository user account [string]
	//opt: password - repository user password [string]
	//returns: html report [string]

	function install($packageUID) {
		//TODO: this - follow dependencies, move install function to kpackage object
		$report .= "";			//%	return value [string]

		/*
		//------------------------------------------------------------------------------------------
		//	try load package manifest and ensure all files have been downloaded
		//------------------------------------------------------------------------------------------
		if (false == hasSource($source)) { $this->addSource($source); }
		$prefix = 'pkg.' . $packageUID . '.';

		$package = new KPackage($packageUID);
		if (false == $package->loaded) { 
			return 'Could not load package manifest.<!-- error -->'; 
		}

		//------------------------------------------------------------------------------------------
		//	add to registry
		//------------------------------------------------------------------------------------------
		$registry->set($prefix . 'source', $source);
		$registry->set($prefix . 'status', 'added');
		if ('' != $username) { $registry->set($prefix . 'username', $username); }
		if ('' != $password) { $registry->set($prefix . 'password', $password); }

		//------------------------------------------------------------------------------------------
		//	download all files belonging to this package
		//------------------------------------------------------------------------------------------
		*/		

		//------------------------------------------------------------------------------------------
		//	check install status, run install function if available
		//------------------------------------------------------------------------------------------

		return $report;
	}

	//==============================================================================================
	//	HTML output
	//==============================================================================================
	
	//----------------------------------------------------------------------------------------------
	//	writes an install status message directly to output
	//----------------------------------------------------------------------------------------------
	//arg: msg - status message [string]
	//opt: color - message box color (black|red|green) [string]

	function log($msg, $color = 'black') {
		echo ''
		 . "<div class='chatmessage" . $color . "'>$msg</div>"
		 . "<script>scrollToEnd();</script>\n";
		flush();
	}

}

?>
