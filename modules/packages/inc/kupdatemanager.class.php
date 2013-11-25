<?

	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

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
//+		pkg.1234567890.dirty	- (yes|no) [string]
//+		pkg.1234567890.r		- Revision number (int) [string]
//+		pkg.1234567890.v		- Version number (int) [string]
//+		pkg.1234567890.date		- Date of last change [string]
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

	var $sources;					//_	list of software sources [array:string]
	var $packages;					//_	set of all available packages [array:array:string]

	var $allFiles;					//_	list of all files in this installation [array]
	var $allFilesLoaded = false;	//_	set to true when all files have been loaded [bool]
	
	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KUpdateManager() {
		global $kapenta;

		$this->sources = $this->listSources();				// get list of sources from registry
		$this->packages = $this->listAllPackages();			// list packages from registry
	}

	//==============================================================================================
	//	sources recorded in registry
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	gets the list of software sources from the registry
	//----------------------------------------------------------------------------------------------
	//returns: array of repository URLs [array:string]

	function listSources() {
		global $kapenta;
		$sources = array();									//%	return value [array]
		$urls = $kapenta->registry->get('kapenta.sources.list');		//%	[string]
		$ary = explode('|', $urls);							//%	[array:string]
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
		global $kapenta;
		if ('' == trim($url)) { return false; }
		if (true == $this->hasSource($url)) { return false; }
		$this->sources[] = $url;
		$ser = implode('|', $this->sources);				//%	serialized array [string]
		$kapenta->registry->set('kapenta.sources.list', $ser);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a software source from the list
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of a kapenta repository [string]
	//returns: true on success, false on failure [bool]

	function removeSource($url) {
		global $kapenta;

		$found = false;				//%	return value [bool]

		//------------------------------------------------------------------------------------------
		//	mark all installed packages from this source as 'nosource'
		//------------------------------------------------------------------------------------------
		$packages = $this->listAllPackages();
		foreach($packages as $pUID) {
			if ($kapenta->registry->get("pkg.$pUID.source") == $url) {
				$kapenta->registry->set("pkg.$pUID.status", 'nosource');
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
		$kapenta->registry->set('kapenta.sources.list', $ser);
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
		global $kapenta;
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
		global $kapenta;
		$packages = array();			//%	all package details [dict:dict]

		$keys = $kapenta->registry->search('pkg', 'pkg.');
		foreach($keys as $key => $val)
		{
			$parts = explode('.', $key);
			if (3 == count($parts))
			{
				$UID = $parts[1];					//% package UID [string]
				$field = $parts[2];					//% package field [string]

				if (false == array_key_exists($UID, $packages))
				{ 
					$packages[$UID] = array(
						'uid' => $UID, 
						'source' => '', 
						'status' => '',
						'name' => '',
						'r' => '',
						'v' => '',
						'manifest' => 'data/packages/' . $UID . '.xml.php'
					);
				}

				$packages[$UID][$field] = $val;
			}
		}

		return $packages;
	}

	//----------------------------------------------------------------------------------------------
	//.	get set of installed packages as array of UIDs
	//----------------------------------------------------------------------------------------------
	//returns: array of package UIDs [array:string]

	function getPackageList() {
		global $kapenta;
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

	function updateAllLists() {
		global $kapenta;
		global $session;

		$report = '';							//%	return value [string]
		$sources = $this->listSources();		//%	software source URLs [array:string]

		//------------------------------------------------------------------------------------------
		//	try update all package lists
		//------------------------------------------------------------------------------------------

		foreach($sources as $url) {
			$source = new KSource($url);
			$this->log('<h2>' . $source->url . '</h2>', 'black');

			$report = $source->update(true);
			//$this->log($report);

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
		global $kapenta;

		$key = 'pkg.' . $packageUID . '.' . $field;
		$old = $kapenta->registry->get($key);
		if ($value != $old) { $kapenta->registry->set($key, $value); }
		if ('status' == $field) { $this->updatePackageList(); }
	}

	//==============================================================================================
	//	ADDING AND REMOVING PACKAGES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	make a list of all local files
	//----------------------------------------------------------------------------------------------

	function getAllFiles() {
		global $kapenta;
		
		if (false == $this->allFilesLoaded) {
			$this->allFiles = $kapenta->fileSearch('', '', true);
			$this->allFilesLoaded = true;
		}
		
		return $this->allFiles;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	check if a package is marked as 'installed' in the registry
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a KPackage [string]

	function isInstalled($UID) {
		global $kapenta;

		if ('installed' == $kapenta->registry->get('pkg.' . $UID . '.status')) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	find the UID of the first package with a given name
	//----------------------------------------------------------------------------------------------
	//arg: packageName - Name of a KPackage [string]
	//returns: UID of first package with that name, empty string on failure [string]

	function findByName($packageName) {
		global $kapenta;

		$matches = $kapenta->registry->search('pkg','pkg');
		foreach($matches as $key => $value) {
			if ((false !== strpos($key, '.name')) && (strtolower($value) == strtolower($packageName))) {
				$key = str_replace('pkg.', '', $key);
				$key = str_replace('.name', '', $key);
				return $key;
			}
		}

		return '';
	}


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
		$kapenta->registry->set($prefix . 'source', $source);
		$kapenta->registry->set($prefix . 'status', 'added');
		if ('' != $username) { $kapenta->registry->set($prefix . 'username', $username); }
		if ('' != $password) { $kapenta->registry->set($prefix . 'password', $password); }

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
		 . "<script>scrollToBottom();</script>\n";
		flush();
	}

}

?>
