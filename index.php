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
	$notifications = new KNotifications();			//	user notification of events
	$revisions = new KRevisions();					//	object revision history and recycle bin
	$utils = new KUtils();							//	miscellaneous
	$sync = new KSync();							//	P2P subsystem

	$request = $req->toArray();						//	(DEPRECATED)
	$ref = $req->ref;								//	(DEPRECATED)

//--------------------------------------------------------------------------------------------------
//	load the current user (public if not logged in)
//--------------------------------------------------------------------------------------------------

	$user = new Users_User($session->user);			//	the user record itself
	$role = new Users_Role($user->role, true);		//	object with user's permissions
	$userlogin = new Users_Login();					//	user's session on the P2P system

	if ('public' != $user->role) {					//	only logged in users can be addressed
		$userlogin->loadUser($user->UID);			
		if (false == $userlogin->loaded) { 			//	create new session is none found
			$userlogin->userUID = $user->UID;
			$userlogin->save();
		}
		$userlogin->updateLastSeen();				//	record that this user is still active
	}

//--------------------------------------------------------------------------------------------------
//	set up the debugger (only admins can toggle debug mode, will persist across multiple logins)
//--------------------------------------------------------------------------------------------------

	if ('admin' == $user->role) {
		if (true == array_key_exists('debug', $req->args)) {
			if ('on' == $req->args['debug']) { $session->debug = true; }
			else { $session->debug = false; }
		}
	}

	$page->logDebug = $session->debug;
	
//--------------------------------------------------------------------------------------------------
//	kapenta environment is set up, load the action requested by the user and pass control
//--------------------------------------------------------------------------------------------------
	$actionFile = $installPath . 'modules/'. $req->module . '/actions/' . $req->action . '.act.php';
	if (false == file_exists($actionFile)) { $page->do404('Unkown action'); }

	include $installPath . 'modules/'. $req->module . '/actions/' . $req->action . '.act.php';

?>
