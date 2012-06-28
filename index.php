<?php

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
//	initialize the registry and system object
//--------------------------------------------------------------------------------------------------

	set_time_limit(900);
	require_once('core/kregistry.class.php');
	require_once('core/ksystem.class.php');

	$registry = new KRegistry();					//	settings registry
	$kapenta = new KSystem();						//	kapenta core

	$request_uri = array_key_exists('q', $_GET) ? $_GET['q'] : '';

//--------------------------------------------------------------------------------------------------
//	include and instantiate the core global objects (database access, templating system, etc)
//--------------------------------------------------------------------------------------------------

	include 'core/core.inc.php';

	$db = new KDBDriver();							//	database wrapper
	$req = new KRequest($request_uri);				//	interpret HTTP request
	$theme = new KTheme($kapenta->defaultTheme);	//	the current theme
	$page = new KPage();							//	document to be returned
	$aliases = new KAliases();						//	handles object aliases
	$notifications = new KNotifications();			//	user notification of events
	$revisions = new KRevisions();					//	object revision history and recycle bin
	$utils = new KUtils();							//	miscellaneous

	$request = $req->toArray();						//	(DEPRECATED)
	$ref = $req->ref;								//	(DEPRECATED)

//--------------------------------------------------------------------------------------------------
//	load the current user (public if not logged in)
//--------------------------------------------------------------------------------------------------

	session_start();
	$session = new Users_Session();					//	user's session
	$user = new Users_User($session->user);			//	the user record itself
	$role = new Users_Role($user->role, true);		//	object with user's permissions
	
	if ('public' != $user->role) {					//	only logged in users can be addressed
		$session->updateLastSeen();					//	record that this session is still active
	}

//--------------------------------------------------------------------------------------------------
//	check for recovery mode
//--------------------------------------------------------------------------------------------------

	if (true == array_key_exists('recover', $req->args)) {
		$pass = $registry->get('kapenta.recoverypassword');
		if (sha1($req->args['recover']) == $pass) {	$session->set('recover', 'yes'); }
	}
	
	if ('yes' == $session->get('recover')) { $user->role = 'admin'; }

//--------------------------------------------------------------------------------------------------
//	check if user originates in our subnet, may redirect others to a central instance
//--------------------------------------------------------------------------------------------------

	if ((false == $req->local) && ('p2p' != $req->module)) {
		$altInstance = $registry->get('kapenta.alternate');
		if (true == array_key_exists('alternate', $req->args)) {
			$session->set('usealternate', $req->args['alternate']);
		}

		if (('' != $altInstance) && ('no' != $session->get('usealternate'))) {
			$URI = str_replace('//', '/', $altInstance . $_SERVER['REQUEST_URI']);
			$URI = str_replace('http:/', 'http://', $URI);
	 		header("HTTP/1.1 301 Moved Permanently");
	 		header("Location: " . $URI); 
			echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
			die();
		}
	}

//--------------------------------------------------------------------------------------------------
//	check fopr mobile browser
//--------------------------------------------------------------------------------------------------

    if ($req->mobile && (false == $session->get('firstdetect'))) {
		$session->set('firstdetect', 'true');
		$session->set('mobile', 'true');
		$session->set('contentWidth', '320');
		$session->msg('Session now in mobile compatability mode.', 'ok');
	} else {
		//	not mobile
	}

//--------------------------------------------------------------------------------------------------
//	set up the debugger
//--------------------------------------------------------------------------------------------------

	if (true == array_key_exists('debug', $req->args)) {
		$auth = false;
		if ('admin' == $user->role) { $auth = true; }
		if (
			(array_key_exists('password', $req->args)) && 
			(sha1($req->args['password']) == $registry->get('kapenta.recoverypassword'))
		) { $auth = true; }

		if ((true == $auth) && ('on' == $req->args['debug'])) { $session->debug = true; }
		else { $session->debug = false; }
	}

	$page->logDebug = $session->debug;
	
//--------------------------------------------------------------------------------------------------
//	kapenta environment is set up, load the action requested by the user and pass control
//--------------------------------------------------------------------------------------------------

	$actionFile = ''
	 . $kapenta->installPath
	 . 'modules/' . $req->module
	 . '/actions/' . $req->action . '.act.php';

	if (false == file_exists($actionFile)) { $page->do404('Unknown action'); }

	require_once($actionFile);

?>
