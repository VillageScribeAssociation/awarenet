<?php

//--------------------------------------------------------------------------------------------------
//	include core component files
//--------------------------------------------------------------------------------------------------

	$coreDir = dirname(__FILE__);

	require_once($coreDir . '/kfilesystem.class.php');		//	access to filesystem
	require_once($coreDir . '/kregistry.class.php');		//	system settings
	require_once($coreDir . '/klog.class.php');				//	logging subsystem
	require_once($coreDir . '/kmemcache.class.php');		//	in-memory cache
	require_once($coreDir . '/krequest.class.php');			//	HTTP request interpreter
	require_once($coreDir . '/kpage.class.php');			//	response document
	require_once($coreDir . '/ktheme.class.php');			//	interface to theme
	require_once($coreDir . '/knotifications.class.php');	//	user notification of events
	require_once($coreDir . '/krevisions.class.php');		//	object revision history and recycle bin
	require_once($coreDir . '/kutils.class.php');			//	miscellaneous utilities
	require_once($coreDir . '/khtml.class.php');			//	html parser
	require_once($coreDir . '/kcache.class.php');			//	block cache
	require_once($coreDir . '/kaliases.class.php');			//	object aliases system

	require_once($coreDir . '/kxmldocument.class.php');		//	xml parser

	//	user, session, role
	require_once($coreDir . '/../modules/users/models/session.mod.php');
	require_once($coreDir . '/../modules/users/models/user.mod.php');
	require_once($coreDir . '/../modules/users/models/role.mod.php');
	require_once($coreDir . '/dbdriver/legacy.class.php');

//--------------------------------------------------------------------------------------------------
//*	model of kapenta system
//--------------------------------------------------------------------------------------------------

class KSystem {

	//----------------------------------------------------------------------------------------------
	//	system components
	//----------------------------------------------------------------------------------------------

	var $components;	//_	array of registered components [array:bool]

	var $fs;			//_	filesystem [object]
	var $db;			//_	database [object]
	var $registry;		//_	stores system and module settings [object]
	var $utils;			//_	various utility methods [object]
	var $blockcache;	//_	database cache (of rendered views) [object]
	var $memcache;		//_	memory cache (reduce disk seeks) [object]
	var $log;			//_	logging subsystem [object]

	//----------------------------------------------------------------------------------------------
	//	CMS components
	//----------------------------------------------------------------------------------------------	

	var $aliases;		//_	human and SEO friendly object references [object]
	var $user;			//_	represents current user [object]
	var $role;			//_	represents user's role and permissions [object]
	var $session;		//_	represents current session [object]

	//----------------------------------------------------------------------------------------------
	//	http mode objects
	//----------------------------------------------------------------------------------------------

	var $request;		//_	interprets browser request [object]
	var $page;			//_	represents response sent to browser [object]
	var $theme;			//_	handles page styles and templating [object]

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
	var $proxyAddress;	//_	proxy address (IP or domain name) [string]
	var $proxyPort;		//_	port number [integer]
	var $proxyUser;		//_	proxy credentials [string]
	var $proxyPass;		//_	proxy credentials [string]

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

	var $loadtime = 0;	//_	used as start time or framework for benchmarking

	var $mc;					//_	holds Memcached client [object]
	var $mcEnabled = false;		//_	set to true if memcached endabled [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: installPath - location of this kapenta installation on disk [string]
	//opt: opts - framework options [string]

	function KSystem($installPath, $opts = '') {
		$this->loadtime = (float)microtime(true);			//	note start time of framework
		$this->installPath = $installPath;
		$this->components = array();						//	included after this
		$this->options = explode(',', $opts);

		$this->register('registry', 'KRegistry');
		$this->register('revisions', 'KRevisions');
		$this->register('blockcache', 'KCache');
		$this->register('aliases', 'KAliases');
		$this->register('notifications', 'KNotifications');
	}

	//----------------------------------------------------------------------------------------------
	//.	register a component
	//----------------------------------------------------------------------------------------------
	//arg: $component - name fo component [string]
	//arg: $className - name of PHP class implementing this core component [string]

	function register($component, $className) {
		$this->components[$component] = array(
			'class' => $className,
			'loaded' => false
		);
	}

	//----------------------------------------------------------------------------------------------
	//.	test if a component is initialized
	//----------------------------------------------------------------------------------------------
	//arg: $component - friendly name of component, used for instantiation on $this [string]

	function isLoaded($component) {
		if (
			(true == array_key_exists($component, $this->components)) && 
			(true == $this->components[$component]['loaded'])
		) {
			return true;
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	initialize a component
	//----------------------------------------------------------------------------------------------
	//arg: $component - friendly name of component, used for instantiation on $this [string]

	function req($component) {
		if (true === $this->isLoaded($component)) { return true; }

		if (false === array_key_exists($component, $this->components)) {
			echo "Unknown core component: $component<br/>\n";
			return false;
		}

		//TODO: return false

		$className = $this->components[$component]['class'];
		$this->$component = new $className;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	initialize framework once $kapenta object has been created
	//----------------------------------------------------------------------------------------------

	function init() {
		$this->initFramework();
		$this->initDb();
		$this->initoptions();
	}	

	//----------------------------------------------------------------------------------------------
	//.	create basic components (filesystem, registry, etc)
	//----------------------------------------------------------------------------------------------

	function initFramework() {
		$this->fs = new KFilesystem($this->installPath);
		//$this->registry = new KRegistry();
		$this->req('registry');
		$this->utils = new KUtils();
		$this->mc = new KMemcache();
	}

	//----------------------------------------------------------------------------------------------
	//.	select and load the database driver
	//----------------------------------------------------------------------------------------------

	function initDb() {
		$dbDriver = $this->registry->get('db.driver');
		if ('' === $dbDriver) { $dbDriver = 'SQLLite'; }
		$this->db = $this->getDbDriver($dbDriver);
	}

	//----------------------------------------------------------------------------------------------
	//.	load optional components
	//----------------------------------------------------------------------------------------------

	function initOptions() {
		foreach($this->options as $opt) {

			switch($opt) {

				case 'cms':			$this->initCms();				break;				
				case 'user':		$this->initUserSession();		break;
				case 'recovery':	$this->initRecovery();			break;

			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	check for recovery mode
	//----------------------------------------------------------------------------------------------

	function initRecovery() {

		if (true == array_key_exists('recover', $this->request->args)) {
			$pass = $this->registry->get('kapenta.recoverypassword');
			if (sha1($this->request->args['recover']) == $pass) {
				$this->session->set('recover', 'yes');
			}
		}
	
		if ('yes' == $this->session->get('recover')) { $this->user->role = 'admin'; }
	}


	//----------------------------------------------------------------------------------------------
	//.	initialize CMS components
	//----------------------------------------------------------------------------------------------

	function initCms() {
		global $_SERVER;

		$request_uri = array_key_exists('q', $_GET) ? $_GET['q'] : '';
		$this->request = new KRequest($request_uri);

		//TODO: remove these globals
		/* global 
			$defaultModule, $defaultTheme, $useBlockCache,
			$rsaKeySize, $rsaPublicKey, $rsaPrivateKey,
			$hostInterface, $proxyEnabled, $proxyAddress, $proxyPort, $proxyUser, $proxyPass,
			$logLevel;
		*/

		//-----------------------------------------------------------------------------------------
		//	get site config from the registry
		//-----------------------------------------------------------------------------------------

		$this->installPath = $this->registry->get('kapenta.installpath');		
		$this->serverPath = $this->registry->get('kapenta.serverpath');
		$this->websiteName = $this->registry->get('kapenta.sitename');
		$this->defaultModule = $this->registry->get('kapenta.modules.default');
		$this->defaultTheme = $this->registry->get('kapenta.themes.default');
		$this->useBlockCache = $this->registry->get('kapenta.blockcache.enabled');
		$this->logLevel = (int)$this->registry->get('kapenta.loglevel');

		//-----------------------------------------------------------------------------------------
		//	guess any missing values
		//-----------------------------------------------------------------------------------------
		if ('' == $this->installPath) { 
			$thisPath = dirname(__FILE__);
			$this->installPath = mb_substr($thisPath, 0, mb_strlen($thisPath) - 5);
			$this->registry->set('kapenta.installpath', $this->installPath); 
		}

		if ('' == $this->serverPath) { 
			$this->serverPath = ''
				. 'http://' . $_SERVER['HTTP_HOST'] 
				. str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);

			$this->registry->set('kapenta.serverpath', $this->serverPath); 
		}

		if ('' == $this->websiteName) { 
			$this->websiteName = 'awareNet';
			$this->registry->set('kapenta.sitename', $this->websiteName); 
		}

		if ('' == $this->defaultTheme) { 
			$this->defaultTheme = 'clockface';
			$this->registry->set('kapenta.themes.default', $this->defaultTheme); 
		}

		if ('' == $this->defaultModule) { 
			$this->defaultModule = 'home';
			$this->registry->set('kapenta.modules.default', $this->defaultModule); 
		}

		//-----------------------------------------------------------------------------------------
		//	set up interface (optional config)
		//-----------------------------------------------------------------------------------------
		$this->hostInterface = $this->registry->get('kapenta.network.interface');
		$this->proxyEnabled = $this->registry->get('kapenta.proxy.enabled');
		$this->proxyAddress = $this->registry->get('kapenta.proxy.address');
		$this->proxyPort = $this->registry->get('kapenta.proxy.port');
		$this->proxyUser = $this->registry->get('kapenta.proxy.user');
		$this->proxyPass = $this->registry->get('kapenta.proxy.password');;

		//-----------------------------------------------------------------------------------------
		//	set up encryption (TODO)
		//-----------------------------------------------------------------------------------------
		$this->rsaKeySize = (int)$this->registry->get('kapenta.rsa.keysize');
		$this->rsaPublicKey = $this->registry->get('kapenta.rsa.publickey');
		$this->rsaPrivate = $this->registry->get('kapenta.rsa.privatekey');

		//-----------------------------------------------------------------------------------------
		//	set up module and theme arrays
		//-----------------------------------------------------------------------------------------
		$this->modules = array();
		$this->modulesLoaded = false;
		$this->themes = array();
		$this->themesLoaded = false;

		//-----------------------------------------------------------------------------------------
		//	initialize core components
		//-----------------------------------------------------------------------------------------

		$this->req('revisions');
		$this->req('blockcache');
		$this->req('aliases');
		$this->req('notifications');
		$this->theme = new KTheme();
		$this->page = new KPage();
	}

	//----------------------------------------------------------------------------------------------
	//.	initialize user session
	//----------------------------------------------------------------------------------------------

	function initUserSession() {

		session_start();

		$this->session = new Users_Session();					//	user's session
		$this->user = new Users_User($this->session->user);		//	the user record itself
		$this->role = new Users_Role($this->user->role, true);	//	object with user's permissions

		if ('public' != $this->user->role) {		//	only for logged in users
			$this->session->updateLastSeen();		//	record that session is still active
		}

		if ('' == $this->session->get('deviceprofile')) {
			$this->session->set('deviceprofile', $this->request->guessDeviceProfile());
		}
	}

	//==============================================================================================
	//	objects
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	create a unique ID 
	//----------------------------------------------------------------------------------------------
	//returns: a new UID, 18 chars long [string]

	function createUID() {
		global $session;


		$tempUID = "";
		$msg = '';
		$isPHP4 = ('4' == substr(phpversion(), 0, 1));
		$start = microtime(true);		

		list($usec, $sec) = explode(' ', microtime());
		$nano_interval = (int)strrev($sec) % 10000;

		for ($i = 0; $i < 18; $i++) {

			$digit = (int)mt_rand(0, 35);

			if ($digit < 10) { $tempUID .= $digit; }		//	[0-9] ASCII
			else { $tempUID .= chr(87 + $digit); }			//	[a-z] ASCII

		}

		$end = microtime(true);
		$msg .= "Calculation time: " . ($end - $start) . " seconds<br/>";

		$tempUID = substr($tempUID, 0, 18);

		//$msg .= "New UID: $tempUID<br/>\n";
		//echo $msg;

		return $tempUID;
	}

	//==============================================================================================
	//	database
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	instantiate a database connection	//	TODO: more checks here
	//----------------------------------------------------------------------------------------------
	//returns: database driver [object]

	function getDBDriver($dbType = 'mysql') {
		global $registry;
		
		if ('' == $dbType) { $this->registry->set('db.driver', 'MySQL'); $dbType = 'MySQL'; }
		include_once($this->installPath . 'core/dbdriver/' . strtolower($dbType) . '.dbd.php');
		$driverName = 'KDBDriver_' . $dbType;
		return new $driverName();
	}

	//----------------------------------------------------------------------------------------------
	//.	instantiate a database connection
	//----------------------------------------------------------------------------------------------
	//returns: database driver [object]

	function getDBAdminDriver() {
		global $registry;
		global $db;

		$dbType = $this->registry->get('db.driver');
		$driverName = 'KDBAdminDriver_' . $dbType;
		include_once($this->installPath . 'core/dbdriver/' . strtolower($dbType) . 'admin.dbd.php');
		return new $driverName($db);
	}

	//==============================================================================================
	//	modules and themes
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	list all modules (enabled/installed or otherwise)
	//----------------------------------------------------------------------------------------------
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
	//	
	//	Note that maxfiles arg is recently introduced to limit slowdown if directories have very
	//	large numbers of entries, eg serialized events
	//
	//arg: path - path relative to installPath [string]
	//opt: ext - file extension	to list, eg '.block.php' [string]
	//opt: maxFiles - maximum number of files to list per directory [int]

	function listFiles($path, $ext = '', $maxFiles = 1024) {
		$fileList = array();
		$maxFiles = 1024;

		$path = str_replace('%%installPath%%', '', $path);
		$path = str_replace($this->installPath, '', $path);
		$extLen = strlen($ext);
		if (false == file_exists($this->installPath . $path)) { return array(); }
		$d = dir($this->installPath . $path);

		while ((false !== ($entry = $d->read())) && ($maxFiles > 0)) {
		  	$entryLen = strlen($entry);
			if ('' != $ext) {
		  		if ( ($entryLen > ($extLen + 1)) AND
			    	 (substr($entry, $entryLen - $extLen) == $ext)) 
						{ $fileList[] = strtolower($entry); }
			} else {
				$fileList[] = $entry;
			}
			$maxFiles --;
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
		
		$adjust = (int)$this->registry->get('kapenta.timedelta');
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
	//	module events, this is a purely push system
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	broadcast an event to a specific module, or to all which support it
	//----------------------------------------------------------------------------------------------
	//arg: module - name of module to notify, or '*' for all [string]
	//arg: event - name of event [string]
	//arg: args - details of event [array]
	//returns: array of status strings, empty string indicates success [array]

	function raiseEvent($module, $event, $args) {
		global $kapenta, $session, $user, $page, $theme, $req, $revisions;

		$outcome = array();

		if (('*' == $module) || ('' == $module)) {
			//--------------------------------------------------------------------------------------
			//	sends event to all modules
			//--------------------------------------------------------------------------------------
			$mods = $this->listModules();
			foreach($mods as $mod) {
				$result = $this->raiseEvent($mod, $event, $args);
				foreach($result as $modName => $errmsg) { $outcome[$modName] = $errmsg; }
			}

		} else {
			//--------------------------------------------------------------------------------------
			//	check if there is an event handler for the module 
			//--------------------------------------------------------------------------------------
			$cbFile = 'modules/' . $module . '/events/' . $event . '.on.php'; 

			if (false == $this->fileExists($cbFile)) {
				$outcome[$module] = '';						//	module does not support this event
				return $outcome;							//	and that's OK
			}

			require_once($this->installPath . $cbFile);
	
			$cbFn = $module . "__cb_" . $event;

			if (false == function_exists($cbFn)) {			//	event handler malformed
				$outcome[$module] = 'No event handler.';	//	treat as error
				return $outcome;
			}

			$outcome[$module] = (string)$cbFn($args);		// do it
		}

		return $outcome;
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
	//	in-memory caching - DEPRECATED, legacy support only, remove when direct calls implemented
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	store an object in the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]
	//arg: objStr - any string, may be a serialized array [string]

	function cacheSet($key, $objStr) {
		return $this->mc->set($key, $objStr);
	}

	//----------------------------------------------------------------------------------------------
	//.	retrieve an object from the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]
	//returns: string reporesentation of the cached item [string]

	function cacheGet($key) {	
		return $this->mc->get($key);
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if an object exists in the cache
	//----------------------------------------------------------------------------------------------

	function cacheHas($key) {
		return $this->mc->has($key);
	}

	//----------------------------------------------------------------------------------------------
	//.	remove an item from the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]

	function cacheDelete($key) {
		return $this->mc->delete($key);
	}

	//----------------------------------------------------------------------------------------------
	//.	clear the entire memcache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]

	function cacheFlush() {
		return $this->mc->flush();
	}

	//==============================================================================================
	//	logging
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	record current page view
	//----------------------------------------------------------------------------------------------
	//TODO: consider adding some of these local variables as members of $kapemta

	function logPageView() {
		global $db, $page, $user, $session;

		$fileName = 'data/log/' . date("y-m-d") . "-pageview.log.php";
		if (false == $this->fileExists($fileName)) { $this->makeEmptyLog($fileName);	}
	
		$referer = '';
		if (true == array_key_exists('HTTP_REFERER', $_SERVER))
			{ $referer = $_SERVER['HTTP_REFERER']; }

		$performance = ''
		 . 'time=' . (microtime(true) - $this->loadtime)
		 . '|queries=' . $this->db->count
		 . '|db_time=' . $this->db->time;

		if (true == function_exists('memory_get_peak_usage')) {
			$peakMemory = memory_get_peak_usage(true);
			$performance .= "|mem=" . $peakMemory . '';
		}

		$remoteHost = $this->session->get('remotehost');
		if ('' == $remoteHost) {

			if (
				('10.' == substr($_SERVER['REMOTE_ADDR'], 0, 3)) ||
				('192.' == substr($_SERVER['REMOTE_ADDR'], 0, 4))
			) {
				$this->session->set('remotehost', $_SERVER['REMOTE_ADDR']);			
			} else {
				$this->session->set('remotehost', gethostbyaddr($_SERVER['REMOTE_ADDR']));
			}
		}

		$userAgent = '';
		if (true == array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
			$userAgentt = $_SERVER['HTTP_USER_AGENT'];
		}

		$entry = "<entry>\n"
			. "\t<timestamp>" . $this->time() . "</timestamp>\n"
			. "\t<mysqltime>" . $this->datetime() . "</mysqltime>\n"
			. "\t<user>" . $this->user->username . "</user>\n"
			. "\t<remotehost>" . $remoteHost . "</remotehost>\n"
			. "\t<remoteip>" . $_SERVER['REMOTE_ADDR'] . "</remoteip>\n"
			. "\t<request>" . $_SERVER['REQUEST_URI'] . "</request>\n"
			. "\t<referrer>" . $referer . "</referrer>\n"
			. "\t<useragent>" . $userAgent . "</useragent>\n"
			. "\t<performace>$performance</performance>\n"
			. "\t<uid>" . $this->page->UID . "</uid>\n"
			. "</entry>\n";

		$result = $this->filePutContents($fileName, $entry, true, false, 'a+');

		if ((microtime(true) - $this->loadtime)	> 5) {
			$msg = 'request=' . $_SERVER['REQUEST_URI'] . '|' . $performance;
			$this->logEvent('page-slow', 'system', 'pageview', $msg);
		}

		//notifyChannel('admin-syspagelog', 'add', base64_encode($entry));
		//$entry = $kapenta->datetime() . " - " . $this->user->username . ' - ' . $_SERVER['REQUEST_URI'];
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

		$result = $this->fs->put($fileName, $defaultLog, true, false, 'w+');
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
		$sessUID = isset($this->session) ? $this->session->UID : 'undefined' ;
		$userUID = isset($this->user) ? $this->user->UID : 'public';

		$entry = "<event>\n";
		$entry .= "\t<datetime>" . $this->datetime() . "</datetime>\n";
		$entry .= "\t<session>" . $sessUID . "</session>\n";
		$entry .= "\t<ip>" . $remoteAddr . "</ip>\n";
		$entry .= "\t<system>" . $subsystem . "</system>\n";
		$entry .= "\t<user>" . $userUID . "</user>\n";
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
		 . "\t<msg>" . htmlentities($msg, ENT_QUOTES, "UTF-8") . "</msg>\n"
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

	//==============================================================================================
	//	filesystem methods / DEPRECATED, legacy support only
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	discover which object owns a file (DEPRECATED)
	//----------------------------------------------------------------------------------------------
	//arg: path - location of file relative to installPath [string]
	//returns: dict of 'module', 'model' and 'UID', empty array on failure [array]

	function fileOwner($path) {
		$this->session->msg('REMOVED: KSystem::fileOwner');
		//return $this->fs->getOwner($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//.	check whether a file exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if file exists, false if not [bool]

	function fileExists($fileName) {
		if (true == $this->isLoaded('session')) {
			$this->session->msg('DEPRECATED: KSystem::fileCheckName');
		}

		return $this->fs->exists($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//.	check a fileName (path) before use
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true, fileName must be inside ../data/ [bool]
	//returns: clean fileName, or false on failure [string][bool]
	
	function fileCheckName($fileName, $inData = false) {
		if (true == $this->isLoaded('session')) {
			$this->session->msg('DEPRECATED: KSystem::fileCheckName');
		}		
		return $this->fs->checkName($fileName, $inData);
	}

	//----------------------------------------------------------------------------------------------
	//.	ensure that a directory exists
	//----------------------------------------------------------------------------------------------
	//arg: fileName - path relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//returns: true on success, false on failure [bool]

	function fileMakeSubdirs($fileName, $inData = false) {
		$this->session->msg('DEPRECATED: KSystem::fileMakeSubdirs');
		return $this->fs->makePath($fileName, $inData);
	}

	//----------------------------------------------------------------------------------------------
	//.	get the contents of a file (entire file returned as string)
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//opt: inData - if true the file must be somewhere in ../data/ [bool]
	//opt: phpWrap - if true any php wrapper will be removed [bool]
	//returns: entire file contents, or false on failure [string][bool]

	function fileGetContents($fileName, $inData = false, $phpWrap = false) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileGetContents');
		return $this->fs->get($fileName, $inData, $phpWrap);
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
		if (true == $this->isLoaded('session')) {
			$this->session->msgAdmin('DEPRECATED: KSystem::filePutContents');
		}		
		return $this->fs->put($fileName, $contents, $inData, $phpWrap, $m);
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function fileDelete($fileName, $inData = false) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileDelete');
		return $this->fs->delete($fileName, $inData);
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a directory
	//----------------------------------------------------------------------------------------------

	function fileRmDir($directory, $inData = false) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileDelete');
		return $this->rmDir($directory, $inData);
	}

	//----------------------------------------------------------------------------------------------
	//.	list the contents of a directory, excluding subdirectories
	//----------------------------------------------------------------------------------------------
	//arg: dir - directory path relative to $kapenta->installPath [string]
	//opt: ext - filter to this file extension, case insensitive [string]
	//opt: onlySubDirs - only returns subdirectories if true [bool]
	//returns: array of file paths relative to installPath [array:string]

	function fileList($dir, $ext = '', $onlySubDirs = false) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileList');
		return $this->fs->listDir($dir, $ext, $onlySubDirs);
	}

	//----------------------------------------------------------------------------------------------
	//.	search for files with a given extension, optionally in some subdirectory
	//----------------------------------------------------------------------------------------------
	//opt: dir - starting directory [string]
	//opt: ext - file extension, eg '.block.php' [string]
	//opt: folders - add directories to the results, default is false [bool]
	//returns: array of file locations [array:string]

	function fileSearch($dir = '', $ext = '', $folders = false) {
        $msg = "DEPRECATED: KSystem::fileSearch ($dir, $ext, $folders)";
        echo $msg;
		$this->session->msgAdmin($msg);
		return $this->fs->search($dir, $ext, $folders);
	}

	//----------------------------------------------------------------------------------------------
	//|	determines if a file/dir exists and is readable + writeable
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if exists, else false [bool]

	function fileIsExtantRW($fileName) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileIsExtantRW');
		return $this->fs->isExtantRW($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//.	remove php wrapper
	//----------------------------------------------------------------------------------------------
	//arg: content - string to remove wrapper from [string]
	//returns: content without wrapper [string]

	function fileRemovePhpWrapper($content) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileRemovePhpWrapper');
		return $this->fs->removePhpWrapper($content);
	}

	//----------------------------------------------------------------------------------------------
	//.	get sha1 hash of file
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: sha1 hash of file, empty string on failure [string]

	function fileSha1($fileName) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileSha1');
		return $this->fs->sha1($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//.	get size of file 
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//returns: size of file in bytes, -1 on failure [int]

	function fileSize($fileName) {		
		$this->session->msgAdmin('DEPRECATED: KSystem::fileSize');
		return $this->fs->size($fileName);
	}

	//----------------------------------------------------------------------------------------------
	//.	copy a file
	//----------------------------------------------------------------------------------------------
	//arg: src - location relative to installPath [string]
	//arg: dest - location relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function fileCopy($src, $dest) {
		$this->session->msgAdmin('DEPRECATED: KSystem::fileCopy');
		return $this->fs->copy($src, $dest);
	}


}

?>
