<?

//--------------------------------------------------------------------------------------------------
//*	model of kapenta system
//--------------------------------------------------------------------------------------------------
//TODO:	consider adding special permission to allow regular users to write files outside of /data/

class KSystem {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $installPath;	//_	location of this kapenta installation on disk [string]
	var $serverPath;	//_	location of this kapenta installation on the network [string]

	var $websiteName;	//_	name of this website [string]

	var $defaultModule;	//_	default module [string]
	var $defaultTheme;	//_	default theme [string]
	var $useBlockCache;	//_	use the block cache? [bool]

	var $hostInterface;	//_	ip address to use when opening sockets [string]
	var $proxyEnabled;	//_ use a web proxy [bool]
	var	$proxyAddress;	//_	proxy address (IP or domain name) [string]
	var	$proxyPort;		//_	port number [integer]
	var	$proxyUser;		//_	proxy credentials [string]
	var	$proxyPass;		//_	proxy credentials [string]

	var $rsaKeySize;	//_	default to 1024 [int]
	var $rsaPublicKey;	//_	plain text of this server's public key [string]
	var $rsaPrivateKey;	//_	plain (clear) text of this server's private key [string]

	var $modules;		//_	nested array describing all modules present on system [array]
	var $modulesLoaded;	//_	set to true when module list has been generated
	var $logLevel;		//_	granularity of log (higher numbers for mroe detail [int]

	var $themes;		//_	nested array describing all themes present on system [array]
	var $themesLoaded;	//_	set to true when themes list has been generated

	var $cronInterval;	//_	minimum time between cron checks, in seconds [int]

	var $coreinc;		//_	flat array describing all inc files in the kapenta core [array]
	var $coreclass;		//_	flat array describing all class files in the kapenta core [array]
	var $corejs;		//_	flat array describing all javascript files in the kapenta core [array]

	var $wrapper = "<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*\n";

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KSystem() {
		global $registry;

		global $installPath, $serverPath, $websiteName;
		global $defaultModule, $defaultTheme, $useBlockCache;
		global $rsaKeySize, $rsaPublicKey, $rsaPrivateKey;
		global $logLevel;
		global $hostInterface, $proxyEnabled, $proxyAddress, $proxyPort, $proxyUser, $proxyPass;

		//-----------------------------------------------------------------------------------------
		//	get site config from the registry
		//-----------------------------------------------------------------------------------------

		$this->installPath = $registry->get('kapenta.installpath');		
		$this->serverPath = $registry->get('kapenta.serverpath');

		$this->websiteName = $registry->get('kapenta.sitename');
		$this->defaultModule = $registry->get('kapenta.modules.default');
		$this->defaultTheme = $registry->get('kapenta.themes.default');
		$this->useBlockCache = $registry->get('kapenta.blockcache.enabled');
		$this->logLevel = (int)$registry->get('kapenta.loglevel');

		//-----------------------------------------------------------------------------------------
		//	guess any missing values
		//-----------------------------------------------------------------------------------------
		if ('' == $this->installPath) { 
			$this->installPath = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
			$registry->set('kapenta.installpath', $this->installPath); 
		}

		if ('' == $this->serverPath) { 
			$this->serverPath = ''
				. 'http://' . $_SERVER['HTTP_HOST'] 
				. str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);

			$registry->set('kapenta.serverpath', $this->serverPath); 
		}

		if ('' == $this->websiteName) { 
			$this->websiteName = 'awareNet';
			$registry->set('kapenta.sitename', $this->websiteName); 
		}

		if ('' == $this->defaultTheme) { 
			$this->defaultTheme = 'clockface';
			$registry->set('kapenta.themes.default', $this->defaultTheme); 
		}

		if ('' == $this->defaultModule) { 
			$this->defaultModule = 'home';
			$registry->set('kapenta.modules.default', $this->defaultModule); 
		}

		//-----------------------------------------------------------------------------------------
		//	set up interface (optional config)
		//-----------------------------------------------------------------------------------------
		$this->hostInterface = $registry->get('kapenta.network.interface');
		$this->proxyEnabled = $registry->get('kapenta.proxy.enabled');
		$this->proxyAddress = $registry->get('kapenta.proxy.address');
		$this->proxyPort = $registry->get('kapenta.proxy.port');
		$this->proxyUser = $registry->get('kapenta.proxy.user');
		$this->proxyPass = $registry->get('kapenta.proxy.password');;

		//-----------------------------------------------------------------------------------------
		//	set up encryption (TODO)
		//-----------------------------------------------------------------------------------------
		$this->rsaKeySize = (int)$registry->get('kapenta.rsa.keysize');
		$this->rsaPublicKey = $registry->get('kapenta.rsa.publickey');
		$this->rsaPrivate = $registry->get('kapenta.rsa.privatekey');

		//-----------------------------------------------------------------------------------------
		//	set up module and theme arrays
		//-----------------------------------------------------------------------------------------
		$this->modules = array();
		$this->modulesLoaded = false;
		$this->themes = array();
		$this->themesLoaded = false;
	}

	//==============================================================================================
	//	objects
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	create a unique ID 
	//----------------------------------------------------------------------------------------------
	//returns: a new UID, 18 chars long [string]

	function createUID() {
		$tempUID = "";
		list($usec, $sec) = explode(' ', microtime());				//	make a seed for rand() ...
		$seed = (float) $sec + ((float) $usec * 100000);			//	is only needed for older PHP
		srand($seed);												//	seed it
		for ($i = 0; $i < 16; $i++) { $tempUID .= "" . rand(); }
		$tempUID = substr($tempUID, 0, 18);
		return $tempUID;
	}

	//==============================================================================================
	//	modules and themes
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	list all modules (enabled/installed or otherwise)
	//----------------------------------------------------------------------------------------------
	//;	Note that static data chould be used on production sites for a slight performance increase?
	//returns: array of modules [array]

	function listModules() {
		$modList = array();		// return value [array]

		if (false == $this->modulesLoaded) {
			//--------------------------------------------------------------------------------------
			//	modules list has not yet been created, create it and cache for future reuse
			//--------------------------------------------------------------------------------------
			$d = dir($this->installPath . 'modules/');
			while (false !== ($entry = $d->read())) {
			  if (($entry != '.') AND ($entry != '..') AND ($entry != '.svn')) {
				$newMod = array();
				$newMod['name'] = strtolower($entry);
				$newMod['abs'] = strtolower($this->installPath .'modules/'. $newMod['name'] .'/');
				$newMod['actions'] = array();
				$newMod['pages'] = array();
				$newMod['views'] = array();
				$newMod['blocks'] = array();
				$newMod['inc'] = array();
				$newMod['actionsLoaded'] = 'no';
				$newMod['pagesLoaded'] = 'no';
				$newMod['viewsLoaded'] = 'no';
				$newMod['incLoaded'] = 'no';
				$newMod['blocksLoaded'] = 'no';
				$this->modules[$newMod['name']] = $newMod;
			  }
			}

			$this->modulesLoaded = true;
		}

		//------------------------------------------------------------------------------------------
		//	make into flat, alphabetical array and return
		//------------------------------------------------------------------------------------------
		foreach($this->modules as $module) { $modList[] = $module['name']; }
		sort($modList);

		return $modList;
	}

	//----------------------------------------------------------------------------------------------
	//.	list all themes 
	//----------------------------------------------------------------------------------------------
	//returns: array of modules [array]

	function listThemes() {
		$themeList = array();		// return value [array]

		if (false == $this->themesLoaded) {
			//--------------------------------------------------------------------------------------
			//	themes list has not yet been created, create it and cache for future reuse
			//--------------------------------------------------------------------------------------
			$d = dir($this->installPath . 'themes/');
			while (false !== ($entry = $d->read())) {
			  if (($entry != '.') AND ($entry != '..') AND ($entry != '.svn')) {
				$newTheme = array();
				$newTheme['name'] = strtolower($entry);
				$newTheme['abs'] = strtolower($this->installPath . 'themes/' . $newTheme['name'] . '/');
				$newTheme['templates'] = array();
				$newTheme['blocks'] = array();
				$newTheme['templatesLoaded'] = 'no';
				$newTheme['blocksLoaded'] = 'no';
				$this->themes[$newTheme['name']] = $newTheme;
			  }
			}

			$this->themesLoaded = true;
		}

		//------------------------------------------------------------------------------------------
		//	make into flat, alphabetical array and return
		//------------------------------------------------------------------------------------------
		foreach($this->themes as $theme) { $themeList[] = $theme['name']; }
		sort($themeList);

		return $themeList;
	}

	//----------------------------------------------------------------------------------------------
	//.	list files of a particular extension (usually .act.php, .view.php, .block.php)
	//----------------------------------------------------------------------------------------------
	//arg: path - path relative to installPath [string]
	//opt: ext - file extension	to list, eg '.block.php' [string]

	function listFiles($path, $ext = '') {
		$fileList = array();

		$path = str_replace('%%installPath%%', '', $path);
		$path = str_replace($this->installPath, '', $path);
		$extLen = strlen($ext);
		if (false == file_exists($this->installPath . $path)) { return array(); }
		$d = dir($this->installPath . $path);

		while (false !== ($entry = $d->read())) {
		  	$entryLen = strlen($entry);
			if ('' != $ext) {
		  		if ( ($entryLen > ($extLen + 1)) AND
			    	 (substr($entry, $entryLen - $extLen) == $ext)) 
						{ $fileList[] = strtolower($entry); }
			} else {
				$fileList[] = $entry;
			}	
		}

		sort($fileList);
		return $fileList;
	}

	//----------------------------------------------------------------------------------------------
	//.	list all actions on a given module
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a module [string]
	//returns: array of actions or false on failure [array][bool]

	function listActions($module) {
		$module = strtolower($module);
		if (false == $this->moduleExists($module)) { return false; }
		if ('no' == $this->modules[$module]['actionsLoaded']) {
			$list = $this->listFiles('modules/' . $module . '/actions/', '.act.php'); 
			$this->modules[$module]['actions'] = $list;
			$this->modules[$module]['actionsLoaded'] = 'yes';
		}
		return $this->modules[$module]['actions'];
	}

	//----------------------------------------------------------------------------------------------
	//.	list all block templates a module provides
	//----------------------------------------------------------------------------------------------
	//arg: module - name of module [string]
	//returns: array of block template files or false on failure [array][bool]

	function listBlocks($module) {
		$module = strtolower($module);
		if (false == $this->moduleExists($module)) { return false; }
		if ('no' == $this->modules[$module]['blocksLoaded']) {
			$list = $this->listFiles('modules/' . $module . '/views/', '.block.php'); 
			$this->modules[$module]['blocks'] = $list;
			$this->modules[$module]['blocksLoaded'] = 'yes';
		}
		return $this->modules[$module]['blocks'];
	}

	//----------------------------------------------------------------------------------------------
	//.	list all pages templates a module provides
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a module [string]
	//returns: array of page template files, or false on failure [array][bool]

	function listPages($module) {
		$module = strtolower($module);
		if (false == $this->moduleExists($module)) { return false; }
		if ('no' == $this->modules[$module]['pagesLoaded']) {
			$list = $this->listFiles('modules/' . $module . '/actions/', '.page.php'); 
			$this->modules[$module]['pages'] = $list;
			$this->modules[$module]['pagesLoaded'] = 'yes';
		}
		return $this->modules[$module]['pages'];
	}

	//----------------------------------------------------------------------------------------------
	//.	list all document templates provided by a theme
	//----------------------------------------------------------------------------------------------
	//arg: theme - name of a kapenta theme [string]
	//returns: array of document templates, or false on failure [array][bool]

	function listTemplates($theme) {
		$theme = strtolower($theme);
		if (false == $this->themeExists($theme)) { return false; }
		if ('no' == $this->themes[$theme]['templatesLoaded']) {
			$list = $this->listFiles('themes/' . $theme . '/', '.template.php');
			$this->themes[$theme]['templates'] = $list;
			$this->themes[$theme]['templatesLoaded'] = 'yes';
		}
		return $this->listFiles('themes/' . $theme . '/', '.template.php');
	}	

	//----------------------------------------------------------------------------------------------
	//.	discover if a module exists
	//----------------------------------------------------------------------------------------------
	//arg: module - name of a module [string]
	//returns: true on success, false on failure [bool]

	function moduleExists($module) {
		$module = strtolower($module);
		if (false == $this->modulesLoaded) { $this->listModules(); }
		foreach($this->modules as $modary) { if ($module == $modary['name']) { return true; } }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a theme exists
	//----------------------------------------------------------------------------------------------
	//arg: theme - name of a theme [string]
	//returns: true on success, false on failure [bool]
	
	function themeExists($theme) {
		$theme = strtolower($theme);
		if (false == $this->themesLoaded) { $this->listThemes(); }
		foreach($this->themes as $themeary) { if ($theme == $themeary['name']) { return true; } }
		return false;
	}

	//==============================================================================================
	//	time and date
	//==============================================================================================
	//TODO: check and overhaul with chat

	//----------------------------------------------------------------------------------------------
	//.	convert a MySQL formatted date (string) to unix timestamp in network time zone
	//----------------------------------------------------------------------------------------------
	//;	note that the p2p network may use a different tiem zone than the local server
	//arg: str - mysql formatted date string [string]
	//returns: unix timestamp [int]

	function strtotime($str) {
		if (true == function_exists('date_default_timezone_set')) {
			//PHP5 only, TODO: figure out what to do about PHP4
			date_default_timezone_set('UTC');
		}
		
		return strtotime($str);
	}

	//----------------------------------------------------------------------------------------------
	//.	get the current unix timestamp in network time zone
	//----------------------------------------------------------------------------------------------
	//returns: unix timestamp of current network time [int]

	function time() {
		global $registry;
		if (true == function_exists('date_default_timezone_set')) {
			//PHP5 only, TODO: figure out what to do about PHP4
			date_default_timezone_set('UTC');
		}
		
		$adjust = (int)$registry->get('kapenta.timedelta');
		$utcTime = time();
		$networkTime = $utcTime + $adjust;

		return $networkTime;
	}

	//----------------------------------------------------------------------------------------------
	//.	get mysql formatted datetime
	//----------------------------------------------------------------------------------------------
	//opt: timestamp - a unix timestamp, set to p2p network time [int]

	function datetime($timestamp = 0) {
		if (true == function_exists('date_default_timezone_set')) {
			//PHP5 only, TODO: figure out what to do about PHP4
			date_default_timezone_set('UTC');
		}

		if (0 == $timestamp) { $timestamp = $this->time(); }
		$date = gmdate("Y-m-d H:i:s", $timestamp);
		return $date;
	}

	//----------------------------------------------------------------------------------------------
	//.	get long format date
	//----------------------------------------------------------------------------------------------
	//opt: datetime - MySQL datetime [string]

	function longDate($datetime = '') {
		if ('' == $datetime) { $datetime = $this->datetime(); }
		$longDate = date('jS F, Y', $this->strtotime($datetime));
		return $longDate;
	}

	//----------------------------------------------------------------------------------------------
	//.	get long format datetime
	//----------------------------------------------------------------------------------------------
	//opt: datetime - MySQL datetime [string]

	function longDatetime($datetime = '') {
		if ('' == $datetime) { $datetime = $this->datetime(); }
		$longDatetime = date('F jS Y h:i', $this->strtotime($datetime));
		return $longDatetime;
	}

	//==============================================================================================
	//	filesystem methods
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	check whether a file exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if file exists, false if not [bool]

	function fileExists($fileName) {
		$fileName = $this->fileCheckName($fileName);
		if (true == file_exists($this->installPath . $fileName)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	check a fileName (path) before use
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true, fileName must be inside ../data/ [bool]
	//returns: clean fileName, or false on failure [string][bool]
	
	function fileCheckName($fileName, $inData = false) {
		$fileName = str_replace('//', '/', $fileName);
		$ipLen = strlen($this->installPath);
		$fileNameLc = strtolower($fileName);		

		//	Unicode directory traversal, see: http://www.schneier.com/crypto-gram-0007.html
		$fileNameLc = str_replace("%c0%af", '/', $fileNameLc);
		$fileNameLc = str_replace("%c0%9v", '/', $fileNameLc);
		$fileNameLc = str_replace("%c1%1c", '/', $fileNameLc);

		//	Precent encoded
		$fileNameLc = str_replace("%2f", '/', $fileNameLc);
		$fileNameLc = str_replace("%5c", '/', $fileNameLc);
		$fileNameLc = str_replace("%2e", '.', $fileNameLc);

		//	Classic directory traversal
		if (strpos(' ' . $fileNameLc, '../') != false) { return false; }
		if (strpos(' ' . $fileNameLc, '..\\') != false) { return false; }

		//	Make absolute locations relative to installPath, case insentitive
		if (strlen($fileName) >= $ipLen) {
			if (strtolower($this->installPath) == substr($fileNameLc, 0, $ipLen)) { 
				$fileName = substr($fileName, $ipLen);
			}
		}

		//	Check that location is inside of ../data/ if required
		if ((true == $inData) && ('data/' != substr($fileNameLc, 0, 5))) { return false; }

		return $fileName;
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure that a directory exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - path relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//returns: true on success, false on failure [bool]

	function fileMakeSubdirs($fileName, $inData = false) {
		$fileName = $this->fileCheckName($fileName, $inData);
		if (false == $fileName) { return false; }
		$dirName = dirname($fileName);

		if (true == file_exists($this->installPath . $dirName . '/')) { 
			// already exists
			return true; 

		} else {
			// doesn't exist, check for and add missing subdirs one at a time
			$base = $this->installPath;
			$subDirs = explode('/', $dirName);
			foreach($subDirs as $sdir) {
				//	note that 'recursive' option for mkdir was only added in PHP 5.0.0
				$base = $base . $sdir . '/';
				if (false == file_exists($base)) {
					$created = mkdir($base); 
					if (false == $created) { return false; }
				}
			}
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the contents of a file (entire file returned as string)
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - if true any php wrapper will be removed [bool]
	//returns: entire file contents, or false on failure [string][bool]

	function fileGetContents($fileName, $inData = false, $phpWrap = false) {
		$fileName = $this->fileCheckName($fileName, $inData);
		if (false == $fileName) { return false; }

		// note that file_get_contents() was added in PHP 4.3, we do it this way to support PHP 4.x
		$fH = @fopen($this->installPath . $fileName, 'rb');		//	specify binary for Windows
		if (false === $fH) { return false; }					//	check that file was opened
		$fileSize = filesize($this->installPath . $fileName);
		if (0 == $fileSize) { return ''; }
		$contents = fread($fH, $fileSize);
		fclose($fH);
		if (true == $phpWrap) { $contents = $this->fileRemovePhpWrapper($contents); }
		return $contents;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the contents of a file, will create directories if they do not exist
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//arg: contents - new file contents [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - protective wrapper [bool]
	//opt: m - file mode [string]
	//returns: true on success, false on failure [bool]

	function filePutContents($fileName, $contents, $inData = false, $phpWrap = false, $m = 'wb+') {
		$fileName = $this->fileCheckName($fileName, $inData);
		if (false == $fileName) { return false; }
		if (false == $this->fileMakeSubdirs($fileName, $inData)) { return false; }

		// add php wrapper to file
		if (true == $phpWrap) { $contents = $this->wrapper . $contents . "\n*/ ?>"; }

		// note that file_put_contents() was added in PHP 5, we do it this way to support PHP 4.x
		$fH = fopen($this->installPath . $fileName, $m);		//	specify binary for Windows
		if (false === $fH) { return false; }					//	can fH ever be 0?
		fwrite($fH, $contents);
		fclose($fH);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function fileDelete($fileName, $inData = false) {
		if (false == $this->fileCheckName($fileName, $inData)) { return false; }
		if (false == $this->fileExists($fileName)) { return false; }
		$check = @unlink($this->installPath . $fileName);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	list the contents of a directory, excluding subdirectories
	//----------------------------------------------------------------------------------------------
	//arg: dir - directory path relative to $kapenta->installPath [string]
	//opt: ext - filter to this file extension, case insensitive [string]
	//opt: onlySubDirs - only returns subdirectories if true [bool]
	//returns: array of file paths relative to installPath [array:string]

	function fileList($dir, $ext = '', $onlySubDirs = false) {
		$list = array();									//%	return value [array:string]

		if (('' == $dir) || ('/' != substr($dir, strlen($dir) - 1))) { $dir = $dir . '/'; }
		$fullPath = $this->installPath . $dir;				//%	relative to root [string]
		$dir = $this->fileCheckName($fullPath);
		$ext = strtolower($ext);
		$extLen = strlen($ext);								//%	length of ext, if any [int]

		$d = dir($fullPath);								//%	directory [object:directory]
		$continue = true;									//%	loop control [bool]
		while (true == $continue) {
			$entry = $d->read();
			$ok = true;

			if (false == $entry) { 
				$ok = false;
				$continue = false;
			}

			if ((true == $ok) && (('.' == $entry) || ('..' == $entry))) { $ok = false; }

			if (true == $ok) {
				$isDir = is_dir($fullPath . $entry);
				if (true == $isDir) { $entry = $entry . '/'; }
				if ($isDir != $onlySubDirs) { $ok = false; }
			} 

			if ((true == $ok) && ('' != $ext) && (strlen($entry) >= $extLen)) {
				$entryLen = strlen($entry);									//%	[int]
				$match = strtolower(substr($entry, $entryLen - $extLen));	//%	[string]
				if ($ext != $match) { $ok = false; }
			}

			if (true == $ok) { $list[] = $dir . $entry; }
		}

		return $list;
	}

	//----------------------------------------------------------------------------------------------
	//.	search for files with a given extension, optionally in some subdirectory
	//----------------------------------------------------------------------------------------------
	//opt: dir - starting directory [string]
	//opt: ext - file extension, eg '.block.php' [string]
	//opt: folders - add directories to the results, default is false [bool]
	//returns: array of file locations [array:string]
	//;	not very efficient, could be improved

	function fileSearch($dir = '', $ext = '', $folders = false) {
		$list = $this->fileList($dir, $ext, false);			//%	return value [array:string]
		$subDirs = $this->fileList($dir, '', true);
		//echo "<pre>\n"; print_r($subDirs); echo "</pre><br/>\n";
		foreach ($subDirs as $subDir) {
			$more = $this->fileSearch($subDir, $ext);
			foreach($more as $item) { $list[] = $item; }
			if (true == $folders) { $list[] = $subDir; }
		}
		return $list;
	}

	//----------------------------------------------------------------------------------------------
	//|	determines if a file/dir exists and is readable + writeable
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if exists, else false [bool]

	function fileIsExtantRW($fileName) {
		$fileName = $this->fileCheckName($fileName);
		if (false == $fileName) { return false; }		// bad file name
		$absolute = $this->installPath . $fileName;
		if (file_exists($absolute)) {
			if (false == is_readable($absolute)) { return false; }
			if (false == is_writable($absolute)) { return false; }
		} else { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove php wrapper
	//----------------------------------------------------------------------------------------------
	//arg: content - string to remove wrapper from [string]
	//returns: content without wrapper [string]

	function fileRemovePhpWrapper($content) {
		$content = trim($content);
		$cL = strlen($content);
		if ($cL < 10) { return $content; }	// too short to be wrapped
		if ("\n*/ ?>" == substr($content, $cL - 6)) { $content = substr($content, 0, ($cL - 6)); }
		if ("\\n*/ ?>" == substr($content, $cL - 7)) { $content = substr($content, 0, ($cL - 7)); }
		if ("<? /*\n" == substr($content, 0, 6)) { $content = substr($content, 6); }
		if ("<? /*\r" == substr($content, 0, 6)) { $content = substr($content, 6); }
		if ($this->wrapper == substr($content, 0, strlen($this->wrapper))) 
			{ $content = substr($content, strlen($this->wrapper)); }

		return $content;
	}

	//----------------------------------------------------------------------------------------------
	//.	get sha1 hash of file
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: sha1 hash of file, empty string on failure [string]

	function fileSha1($fileName) {
		$hash = '';
		if (true == $this->fileExists($fileName)) {
			$hash = sha1_file($this->installPath . $fileName);
		}
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	get size of file 
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: size of file in bytes, -1 on failure [int]

	function fileSize($fileName) {		
		$size = -1;														//%	return value [int]
		if (true == $this->fileExists($fileName)) {
			$size = filesize($this->installPath . $fileName);
		}
		return $size;
	}

	//----------------------------------------------------------------------------------------------
	//.	copy a file
	//----------------------------------------------------------------------------------------------
	//arg: src - location relative to installPath [string]
	//arg: dest - location relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function fileCopy($src, $dest) {
		$check = false;								//%	return false [bool]
		$src = $this->fileCheckName($src);
		$dest = $this->fileCheckName($dest);
		if ((false == $src) || (false == $dest)) { return $check; }
		if (false == $this->fileExists($src)) { return $check; }
		$check = $this->filemakeSubDirs($dest);
		if (false == $check) { return false; }
		$check = copy($this->installPath . $src, $this->installPath . $dest);
		return $check;
	}

	//==============================================================================================
	//	module events, this is a purely push system
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	sends an event to a specific module
	//----------------------------------------------------------------------------------------------
	//arg: module - name of module to notify, or '*' for all [string]
	//arg: event - name of event [string]
	//arg: args - details of event [array]
	//returns: reserved [array]

	function raiseEvent($module, $event, $args) {
		global $kapenta, $session, $user, $page, $theme, $req, $revisions;
		if (('*' == $module) || ('' == $module)) {
			//--------------------------------------------------------------------------------------
			//	sends event to all modules
			//--------------------------------------------------------------------------------------
			$mods = $this->listModules();
			foreach($mods as $mod) { $this->raiseEvent($mod, $event, $args); }

		} else {
			//--------------------------------------------------------------------------------------
			//	check if there is an event handler for the module 
			//--------------------------------------------------------------------------------------
			$cbFile = 'modules/' . $module . '/events/' . $event . '.on.php'; 
			if (false == $this->fileExists($cbFile)) { return false; }	
			require_once($this->installPath . $cbFile);
	
			$cbFn = $module . "__cb_" . $event;
			if (false == function_exists($cbFn)) { return false; }		// handles this event?
			$result = $cbFn($args);
			return array($result);										// do it
		}
	}

	//==============================================================================================
	//	object relationships
	//==============================================================================================

	function relationshipExists($module, $model, $UID, $relationship, $userUID) {
		global $kapenta, $theme, $user, $db, $session, $req,
				$page, $aliases, $notifications, $utils, $sync;		// make available to $relFile

		if (false == $this->moduleExists($module)) { return false; }
		
		$relFile = 'modules/' . $module . '/inc/relationships.inc.php';
		if (false == $this->fileExists($relFile)) { return false; }	
		require_once($this->installPath . $relFile);
	
		$relFn = $module . "_relationships";
		if (false == function_exists($relFn)) { return false; }		// provides relationship?
		$result = $relFn($model, $UID, $relationship, $userUID);	// check
		return $result;												// do it
	}


	//==============================================================================================
	//	logging
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	record current page view
	//----------------------------------------------------------------------------------------------
	//TODO: consider adding some of these local variables as members of $kapemta

	function logPageView() {
		global $db, $page, $user;

		$fileName = 'data/log/' . date("y-m-d") . "-pageview.log.php";
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
	
		$referer = '';
		if (true == array_key_exists('HTTP_REFERER', $_SERVER))
			{ $referer = $_SERVER['HTTP_REFERER']; }

		$entry = "<entry>\n"
			. "\t<timestamp>" . $this->time() . "</timestamp>\n"
			. "\t<mysqltime>" . $this->datetime() . "</mysqltime>\n"
			. "\t<user>" . $user->username . "</user>\n"
			. "\t<remotehost>" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "</remotehost>\n"
			. "\t<remoteip>" . $_SERVER['REMOTE_ADDR'] . "</remoteip>\n"
			. "\t<request>" . $_SERVER['REQUEST_URI'] . "</request>\n"
			. "\t<referrer>" . $referer . "</referrer>\n"
			. "\t<useragent>" . $_SERVER['HTTP_USER_AGENT'] . "</useragent>\n"
			. "\t<uid>" . $page->UID . "</uid>\n"
			. "</entry>\n";

		$result = $this->filePutContents($fileName, $entry, true, false, 'a+');

		//notifyChannel('admin-syspagelog', 'add', base64_encode($entry));
		//$entry = $kapenta->datetime() . " - " . $user->username . ' - ' . $_SERVER['REQUEST_URI'];
		//notifyChannel('admin-syspagelogsimple', 'add', base64_encode($entry));

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	create an empty log file TODO: use filePutContents
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]

	function makeEmptyLog($fileName) {
		//TODO: fix this to work without setup.inc.php
		$defaultLog = "<?\n" 
					. "\tinclude '../../index.php';\n"
					. "\tlogErr('log', 'eventLog', 'direct access by browser');\n"
					. "\tdo404();\n"
					. "?>\n\n";

		$result = $this->filePutContents($fileName, $defaultLog, true, false, 'w+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a system event
	//----------------------------------------------------------------------------------------------
	//arg: log - log name [string]
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logEvent($log, $subsystem, $fn, $msg) {
		global $user, $db, $session;

		//------------------------------------------------------------------------------------------
		//	create new log files as necessary and try get user's IP address
		//------------------------------------------------------------------------------------------
		$fileName = 'data/log/' . date("y-m-d") . '-' . $log . '.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName); }
	
		$remoteAddr = '';
		if (true == array_key_exists('REMOTE_ADDR', $_SERVER))
			{ $remoteAddr = $_SERVER['REMOTE_ADDR']; }

		//------------------------------------------------------------------------------------------
		//	add a new entry to the log file
		//------------------------------------------------------------------------------------------
		$entry = "<event>\n";
		$entry .= "\t<datetime>" . $this->datetime() . "</datetime>\n";
		$entry .= "\t<session>" . $session->UID . "</session>\n";
		$entry .= "\t<ip>" . $remoteAddr . "</ip>\n";
		$entry .= "\t<system>" . $subsystem . "</system>\n";
		$entry .= "\t<user>" . $user->UID . "</user>\n";
		$entry .= "\t<function>" . $fn . "</function>\n";
		$entry .= "\t<msg>$msg</msg>\n";
		$entry .= "</event>\n";

		$result = $this->filePutContents($fileName, $entry, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	record an ordinary system event
	//----------------------------------------------------------------------------------------------
	//arg: granularity - level of detail (0-3) [int]
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logEv($granularity, $subsystem, $fn, $msg) {
		if ($this->logLevel < $granularity) { return false; }
		return $this->logEvent('event', $subsystem, $fn, $msg); 
	}

	//----------------------------------------------------------------------------------------------
	//.	record an error
	//----------------------------------------------------------------------------------------------
	//arg: subsystem - subsystem name/label [string]
	//arg: fn - function name [string]
	//arg: msg - message to log [string]
	//returns: true on success or false on failure [bool]

	function logErr($subsystem, $fn, $msg) { 
		return $this->logEvent('error', $subsystem, $fn, $msg); 
	}

	//----------------------------------------------------------------------------------------------
	//.	log sync activity
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: remove when protocol changeover is complete
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logSync($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-sync.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = $this->datetime() . " **************************************************\n". $msg;
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log p2p activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logP2P($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-p2p.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = $this->datetime() . " **************************************************\n". $msg;
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log p2p activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logCron($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-cron.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = ''
		 . "<event>\n"
		 . "\t<time>" . $this->datetime() . "</time>\n"
		 . "\t<msg>" . htmlentities($msg) . "</msg>\n"
		 . "</event>\n";
		$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	log live activity
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log [string]
	//: this is overused due to development, needs to be trimmed out of a lot of places now
	//: that the sync module is pretty stable.

	function logLive($msg) {
		global $db;
		$fileName = 'data/log/' . date("y-m-d") . '-live.log.php';
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
		$msg = "\n" . $msg;
		$result = false;
		//$result = $this->filePutContents($fileName, $msg, true, false, 'a+');
		return $result;
	}

	//==============================================================================================
	//	web shell
	//==============================================================================================
	
	//----------------------------------------------------------------------------------------------
	//.	check if a shell command is implemented (basic, only checks for file)
	//----------------------------------------------------------------------------------------------
	//arg: canonical - full name of command, eg live.clear [string]
	//returns: true if command exists, false if not [bool]

	function shellCmdExists($canonical) {
		if (false == strpos($canonical, '.')) { return false; }		
		$parts = explode('.', $canonical, 2);
		$module = $parts[0];
		$method = $parts[1];

		$fileName = 'modules/' . $module . '/shell/' . $method . '.cmd.php';

		if (true == $this->fileExists($fileName)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if a shell command is implemented (basic, only checks for file)
	//----------------------------------------------------------------------------------------------
	//arg: canonical - full name of command, eg live.clear [string]
	//arg: args - arguments array [array:string]
	//returns: html command report [string]

	function shellExecCmd($canonical, $args) {
		global $kapenta, $theme, $user, $db, $session, $req,
				$page, $aliases, $notifications, $utils, $sync;		// make available to command

		if (false == $this->shellCmdExists($canonical)) { return 'Command not recognized'; }		
		$parts = explode('.', $canonical, 2);
		$module = $parts[0];
		$method = $parts[1];

		$fileName = 'modules/' . $module . '/shell/' . $method . '.cmd.php';
		require_once($fileName);

		$functionName = $module . '_WebShell_' . $method;
		if (false == function_exists($functionName)) { return 'Function not implemented.'; }
	
		return $functionName($args);
	}

	//----------------------------------------------------------------------------------------------
	//.	display built-in help for a web shell command
	//----------------------------------------------------------------------------------------------
	//arg: canonical - canonical name of a web shell command (module.method) [string]
	//arg: short - display short form help [bool]
	//returns: manpage [string:html]

	function shellCmdHelp($canonical, $short) {
		global $kapenta, $theme, $user, $db, $session, $req,
				$page, $aliases, $notifications, $utils, $sync;		// make available to command

		if (false == $this->shellCmdExists($canonical)) { 
			return "<span style='ajaxwarn'>Command not recognized.</span>"; 
		}
		
		$parts = explode('.', $canonical, 2);
		$module = $parts[0];
		$method = $parts[1];

		$fileName = 'modules/' . $module . '/shell/' . $method . '.cmd.php';
		require_once($fileName);

		$functionName = $module . '_WebShell_' . $method . '_help';
		if (false == function_exists($functionName)) { 
			return "<span class='ajaxwarn'>Help function not implemented.</span>"; 
		}

		return $functionName($short);
	}

	//==============================================================================================
	//	processes
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	start a process in background (*nix only)
	//----------------------------------------------------------------------------------------------
	//:	source: http://nsaunders.wordpress.com/2007/01/12/running-a-background-process-in-php/
	//:	TODO: find equivalent for windows
	//: will probably not work in safe mode, forking in PHP is too large a topic to be covered here
	//arg: Command - shell command [string]
	//arg: Priority - runlevel [int]
	//returns: ID of new process (PID) [int]

	function procExecBackground($Command, $Priority = 0) {
		$PID = false;
		if ($Priority) { 
			// TODO: consider removing this
			$PID = exec("$Command > > /dev/null 2>&1 &"); 
		} else { 
			$PID = exec("$Command > /dev/null 2>&1 &");
		}
		return $PID;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a process is running
	//----------------------------------------------------------------------------------------------
	//:	source: http://nsaunders.wordpress.com/2007/01/12/running-a-background-process-in-php/
	//: *Nix only, might work on windows with Cygwin or GNU tools, probably won't work in safe mode
	//arg: PID - process ID [int] [string]
	//returns: true if running, otherwise false [bool]

	function procIsRunning($PID) {
		exec("ps $PID", $ProcessState);
		return(count($ProcessState) >= 2);
	}

	//----------------------------------------------------------------------------------------------
	//.	clean up the temp directory
	//----------------------------------------------------------------------------------------------
	//:	files stored in the temp directory should begin with a timestamp and then a hyphen.
	//:	files older than one hour are deleted.	
	//returns: html report [string]

	function procCleanTemp() {
		$report = '';			//%	return value [string]
		if (false == $this->fileExists('data/temp/')) { return false; }
		$d = dir($this->installPath . 'data/temp/');
		
		while (false !== ($entry = $d->read())) {
			$isFile = true;
			if ('.' == $entry) { $isFile = false; }
			if ('..' == $entry) { $isFile = false; }
			// TODO: check for directories

			if (true == $isFile) {
				$report .= "temp file: " . $entry . "<br/>\n";
				if (false === strpos($entry, '-')) {
					$report .= "misnamed file: " . $entry . " (no hyphen, removed)<br/>\n";
					unlink($this->installPath . 'data/temp/' . $entry);

				} else {
					//------------------------------------------------------------------------------	
					// look for old files
					//------------------------------------------------------------------------------
					$parts = explode('-', $entry, 2);
					$timestamp = (int)$parts[0];
					$hourago = $this->time() - (3600);	// TODO: make a setting, remove literal
					if ($timestamp < $hourago) { 
						$report .= "old file: " . $entry . " (removed)<br/>\n";
						unlink($this->installPath . 'data/temp/' . $entry);
					}

				} // end if has hyphen
			} // end if is file
		} // end while

		return $report;
	}

}

?>
