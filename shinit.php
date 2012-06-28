<?

//--------------------------------------------------------------------------------------------------
//*	initialize environment for CLI PHP scripts
//--------------------------------------------------------------------------------------------------
	if ('cli' != strtolower(PHP_SAPI)) { die("This script must be run from the console.\n"); }

//--------------------------------------------------------------------------------------------------
//	spoof a web server (clumsily)
//--------------------------------------------------------------------------------------------------
	
	$_SERVER['HTTP_HOST'] = '127.0.0.1';
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

//--------------------------------------------------------------------------------------------------
//	load kapenta environment
//--------------------------------------------------------------------------------------------------

	include dirname(__FILE__) . '/core/kregistry.class.php';
	include dirname(__FILE__) . '/core/ksystem.class.php';

	$registry = new KRegistry(dirname(__FILE__) . '/');		//	system settings
	$kapenta = new KSystem();						//	object for interacting with core	

	#echo "kapenta.installpath: " . $registry->get('kapenta.installpath') . "\n";
	#echo "kapenta.serverpath: " . $registry->get('kapenta.serverpath') . "\n";	

	include dirname(__FILE__) . '/core/core.inc.php';

//--------------------------------------------------------------------------------------------------
//	important global objects
//--------------------------------------------------------------------------------------------------
	$db = new KDBDriver();							//	database wrapper
	$req = new KRequest('/admin/shell/');				//	interpret HTTP request
	$theme = new KTheme($kapenta->defaultTheme);	//	the current theme
	$page = new KPage();							//	document to be returned
	$aliases = new KAliases();						//	handles object aliases
	$notifications = new KNotifications();			//	user notification of events
	$revisions = new KRevisions();					//	object revision history and recycle bin
	$utils = new KUtils();							//	miscellaneous

	$request = $req->toArray();						//	(DEPRECATED)
	$ref = $req->ref;								//	(DEPRECATED)

//--------------------------------------------------------------------------------------------------
//	load the current user (admin at shell)
//--------------------------------------------------------------------------------------------------
	session_start();
	$session = new Users_Session();					//	user's session
	$user = new Users_User($session->user);			//	the user record itself
	$role = new Users_Role('admin', true);			//	object with user's permissions
	
	if ('public' != $user->role) {					//	only logged in users can be addressed
		$session->updateLastSeen();					//	record that this session is still active
	}
	
//--------------------------------------------------------------------------------------------------
//	kapenta environment is set up, ready to run shell script
//--------------------------------------------------------------------------------------------------

?>
