<?

//--------------------------------------------------------------------------------------------------
//*	default libraries and objects available to all modules
//--------------------------------------------------------------------------------------------------
//+	Default Objects:
//+
//+		$kapenta - abstraction of kapenta installation [class:KSystem]
//+		$req - abstraction of request made by browser [class:KRequest]
//+		$page - abstraction of the response document [class:KPage]
//+		$db - abstraction of the database [class:KDBDriver]

//--------------------------------------------------------------------------------------------------
//	default objects
//--------------------------------------------------------------------------------------------------

	//include 'kregistry.class.php';		// settings registry
	//include 'ksystem.class.php';		// system
	include 'kxmldocument.class.php';	// xml parser
	include 'ksession.class.php';		// HTTP request interpreter
	include 'krequest.class.php';		// HTTP request interpreter
	include 'kpage.class.php';			// response document
	include 'ktheme.class.php';			// interface to theme
	include 'ksync.class.php';			// peer-to-peer subsystem
	include 'knotifications.class.php';	// user notification of events
	include 'krevisions.class.php';		// object revision history and recycle bin
	include 'kutils.class.php';			// miscellaneous utilities

	$dbDriver = '';						// TODO: add other DBMS here
	$dbType = 'MySql';

	switch($dbType) {
		case 'MySQL':	$dbDriver = 'core/dbdriver/mysql.dbd.php';
		default:		$dbDriver = 'core/dbdriver/mysql.dbd.php';
	}

	include $dbDriver;
	include 'core/dbdriver/mysqladmin.dbd.php';

	include 'core/kaliases.class.php';
	include 'modules/users/models/user.mod.php';
	include 'modules/users/models/role.mod.php';

//--------------------------------------------------------------------------------------------------
//	older library stubs (deprecated)
//--------------------------------------------------------------------------------------------------

	//include 'http.inc.php';
	//include 'routing.inc.php';
	//include 'mysql.inc.php';	
	//include 'recordalias.inc.php';
	//include 'utils.inc.php';
	//include 'session.inc.php';
	//include 'xml.inc.php';
	//include 'blocks.inc.php';
	//include 'log.inc.php';
	//include 'theme.inc.php';
	//include 'img.inc.php';
	//include 'auth.inc.php';
	//include 'notifications.inc.php';
	//include 'modutils.inc.php';
	//include 'events.inc.php';
	//include 'sync.inc.php';

?>
