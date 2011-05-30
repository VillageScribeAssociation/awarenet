<?

//--------------------------------------------------------------------------------------------------
//*	initialize environment for CLI PHP scripts
//--------------------------------------------------------------------------------------------------
	if ('cli' != strtolower(PHP_SAPI)) { die("This script must be run from the console.\n"); }

	include 'core/kregistry.class.php' ;
	include 'core/ksystem.class.php' ;

	$registry = new KRegistry();					//	system settings
	$kapenta = new KSystem();						//	object for interacting with core	

	include 'core/core.inc.php';

//--------------------------------------------------------------------------------------------------
//	spoof a web server (clumsily)
//--------------------------------------------------------------------------------------------------
	
	//TODO: this

//--------------------------------------------------------------------------------------------------
//	important global objects
//--------------------------------------------------------------------------------------------------
	$session = new KSession();						//	current user session
	$db = new KDBDriver();							//	database wrapper
	$req = new KRequest('/shell/');					//	interpret HTTP request
	$theme = new KTheme($kapenta->defaultTheme);	//	the current theme
	$page = new KPage();							//	document to be returned
	$aliases = new KAliases();						//	handles object aliases
	$notifications = new KNotifications();			//	user notification of events
	$revisions = new KRevisions();					//	object revision history and recycle bin
	$utils = new KUtils();							//	miscellaneous
	$sync = new KSync();							//	P2P subsystem

//--------------------------------------------------------------------------------------------------
//	load the current user (public if not logged in)
//--------------------------------------------------------------------------------------------------
	$user = new Users_User($kapenta->shellUser);	//	the user record itself
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

?>
