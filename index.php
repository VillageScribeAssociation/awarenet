<?

//--------------------------------------------------------------------------------------------------
//		 _                          _                                _    
//		| | ____ _ _ __   ___ _ __ | |_ __ _   ___  _ __ __ _  _   _| | __
//		| |/ / _` | '_ \ / _ \ '_ \| __/ _` | / _ \| '__/ _` || | | | |/ /
//		|   < (_| | |_) |  __/ | | | || (_| || (_) | | | (_| || |_| |   < 
//		|_|\_\__,_| .__/ \___|_| |_|\__\__,_(_)___/|_|  \__, (_)__,_|_|\_\
//		          |_|                                   |___/     
//                                                                           	Version 2.0 Beta
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	include the kapenta core functions (database access, templating system, etc)
//--------------------------------------------------------------------------------------------------
	
	include 'setup.inc.php';
	include $installPath . 'core/core.inc.php';
	
//--------------------------------------------------------------------------------------------------
//	important global objects
//--------------------------------------------------------------------------------------------------

	$kapenta = new KSystem();						//	object for interacting with core
	$session = new KSession();						//	current user session
	$db = new KDBDriver();							//	database wrapper
	$req = new KRequest($_SERVER['REQUEST_URI']);	//	interpret HTTP request
	$theme = new KTheme($kapenta->defaultTheme);	//	the current theme
	$page = new KPage();							//	document to be returned
	$aliases = new KAliases();						//	handles object aliases
	$notifications = new KNotifications();			//	User notification of events
	$utils = new KUtils();							//	miscellaneous
	$sync = new KSync();							//	P2P subsystem

	$request = $req->toArray();						//	(DEPRECATED)
	$ref = $req->ref;								//	(DEPRECATED)

//--------------------------------------------------------------------------------------------------
//	load the current user (public if not logged in)
//--------------------------------------------------------------------------------------------------

	$user = new Users_User($session->user);
	$role = new Users_Role($user->role, true);

	// toggle debug mode
	if (true == array_key_exists('debug', $req->args)) {
		if ('on' == $req->args['debug']) { $session->debug = true; }
		else { $session->debug = false; }
	}
	$page->logDebug = $session->debug;
	
//--------------------------------------------------------------------------------------------------
//	load the action requested by the user
//--------------------------------------------------------------------------------------------------

	include $installPath . 'modules/'. $req->module . '/actions/' . $req->action . '.act.php';

?>
