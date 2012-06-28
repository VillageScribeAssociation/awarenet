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

	$cdr = dirname(__FILE__);

	#include $cdr . '/kregistry.class.php';		// settings registry
	#include $cdr . '/ksystem.class.php';		// system
	include $cdr . '/kxmldocument.class.php';	// xml parser
	#include $cdr . '/ksession.class.php';		// HTTP request interpreter
	include $cdr . '/krequest.class.php';		// HTTP request interpreter
	include $cdr . '/kpage.class.php';			// response document
	include $cdr . '/ktheme.class.php';			// interface to theme
	#include $cdr . '/ksync.class.php';			// peer-to-peer subsystem
	include $cdr . '/knotifications.class.php';	// user notification of events
	include $cdr . '/krevisions.class.php';		// object revision history and recycle bin
	include $cdr . '/kutils.class.php';			// miscellaneous utilities
	include $cdr . '/khtml.class.php';			// html parser

	$dbDriver = '';						// TODO: add other DBMS here
	$dbType = 'MySql';

	switch($dbType) {
		case 'MySQL':	$dbDriver = $cdr . '/dbdriver/mysql.dbd.php';
		default:		$dbDriver = $cdr . '/dbdriver/mysql.dbd.php';
	}

	include $dbDriver;
	include $cdr . '/dbdriver/mysqladmin.dbd.php';

	include $cdr . '/kaliases.class.php';
	include $cdr . '/../modules/users/models/session.mod.php';
	include $cdr . '/../modules/users/models/user.mod.php';
	include $cdr . '/../modules/users/models/role.mod.php';

?>
