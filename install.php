<?

//==================================================================================================
//	INSTALL AWARENET 3b FROM REPOSITORY
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	create install session and fill in as many variables as possible
//--------------------------------------------------------------------------------------------------

	session_start();
	// admin user
	if (!(array_key_exists('iUser', $_SESSION))) { $_SESSION['iUser'] = 'admin'; }
	if (!(array_key_exists('iAUser', $_SESSION))) { $_SESSION['iAUser'] = 'admin'; }
	if (!(array_key_exists('iAPass1', $_SESSION))) { $_SESSION['iAPass1'] = ''; }
	if (!(array_key_exists('iAPass2', $_SESSION))) { $_SESSION['iAPass2'] = ''; }
	if (!(array_key_exists('sUser', $_SESSION))) { $_SESSION['sUser'] = 'admin'; }

	// install path
	if (!(array_key_exists('iInstallPath', $_SESSION))) 
		{ $_SESSION['iInstallPath'] = dirname($_SERVER['SCRIPT_FILENAME']); }

	// server path
	if (!(array_key_exists('iServerPath', $_SESSION))) 
		{ $_SESSION['iServerPath'] = dirname($_SERVER['REQUEST_URI']); }

	// database
	if (!(array_key_exists('iDbName', $_SESSION))) { $_SESSION['iDbName'] = ''; }
	if (!(array_key_exists('iDbHost', $_SESSION))) { $_SESSION['iDbHost'] = ''; }
	if (!(array_key_exists('iDbUser', $_SESSION))) { $_SESSION['iDbUser'] = ''; }
	if (!(array_key_exists('iDbPass', $_SESSION))) { $_SESSION['iDbPass'] = ''; }

//--------------------------------------------------------------------------------------------------
//	globals
//--------------------------------------------------------------------------------------------------

	$repositoryList = 'http://www.kapenta.org.uk/code/projectlist/project_106665425118526027';
	$repositoryDoor = 'http://www.kapenta.org.uk/code/projectfile/file_';

	$testsOk = false;
	$report = '';

	$maxRetries = 3;

//--------------------------------------------------------------------------------------------------
//	get POST vars from form
//--------------------------------------------------------------------------------------------------

	foreach($_SESSION as $key => $val) {
		if (array_key_exists($key, $_POST) == true) { $_SESSION[$key] = $_POST[$key]; }
	}

//--------------------------------------------------------------------------------------------------
//	perform tests using these vars
//--------------------------------------------------------------------------------------------------

	printHeader();

	$report = preTests();
	if (strpos(' ' . $report, '[*]') == false) { $testsOk = true; }

	if ($testsOk == true) {
		//------------------------------------------------------------------------------------------
		//	try to create setup.inc.php
		//------------------------------------------------------------------------------------------		
		$setupFile = $_SESSION['iInstallPath'] . 'setup.inc.php';
		$testsOk = saveSetupIncPhp($setupFile);
		if ($testsOk == false) { 
			$report .= "[*] Could not create setup.inc.php, please check file permissions.<br/>\n"
					 . "[i] install in directory: " . $_SESSION['iInstallPath'] . " <br/>\n"; 
			
		}
	}
	
	if ($testsOk == false) {
		//------------------------------------------------------------------------------------------
		//	show install form, we need to fill/correct variables
		//------------------------------------------------------------------------------------------
		showInstallForm();
		echo $report;

	} else {
		//------------------------------------------------------------------------------------------
		//	do it
		//------------------------------------------------------------------------------------------
		$report .= "[>] Installing Awarenet 3b from code repository...<br/>\n"; flush();
		echo $report;

		// create database tables (todo: utilise install.inc.php on modules)
		echo "<h2>Installing database...</h2>\n";		
		makeDefaultTables();

		// download all files from repository
		echo "<h2>Downloading modules from repository...</h2>\n";		
		$itemList = getRepositoryList($repositoryList);						// get list of items
		$retryList = downloadFromRepository($itemList, $repositoryDoor);	// download each item

		// retry any files which failed
		for ($i = 0; $i < $maxRetries; $i++) {
			if (count($retryList) > 0) {
				echo "<h1>Retrying... ($i) </h1>\n";
				$retryList = downloadFromRepository($retryList, $repositoryDoor);		
			}
		}

		// print list of any files which failed after three attempts

		// 302 to home page
		echo "<br/><br/><a href='" . $_SESSION['iServerPath'] . "'>" 
			. "[all done, continue to front page >> ]</a><br/>"
			. "<script> setTimeout(\"window.location='" . $_SESSION['iServerPath'] 
			. "';\",5000); </script>";

	}

	printFooter();

//--------------------------------------------------------------------------------------------------
//	** end of install script **
//--------------------------------------------------------------------------------------------------

//==================================================================================================
//	UTILITY FUNCTIONS FOR THIS INSTALL SCRIPT
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	run pre-install tests (database, write access, mod rewrite)
//--------------------------------------------------------------------------------------------------

function preTests() {
	$report = '';
	
	//----------------------------------------------------------------------------------------------
	// to do with admin user
	//----------------------------------------------------------------------------------------------

	if ($_SESSION['iAPass1'] != $_SESSION['iAPass2']) 
		{ $report .= "[*] Passwords do not match.<br/>\n"; }

	if (strlen(trim($_SESSION['iAPass1'])) < 4)
		{ $report .= "[*] Please choose a password of more than four characters.<br/>\n"; }

	if (strlen($_SESSION['iAUser']) < 4) 
		{ $report .= "[*] Administrator user name should be at least four chars.<br/>\n"; }

	//----------------------------------------------------------------------------------------------
	// to do with file paths and server paths
	//----------------------------------------------------------------------------------------------

	if (substr(strrev($_SESSION['iInstallPath']), 0, 1) != '/') 
		{ $report .= "[*] Install path should end with a trailing slash.<br/>\n"; }
	
	if (substr(strrev($_SESSION['iServerPath']), 0, 1) != '/') 
		{ $report .= "[*] Server path should end with a trailing slash.<br/>\n"; }

	if (is_extantrw($_SESSION['iInstallPath']) == false) {
		$report .= "[*] Check install path exists and permissions are set +r+w+x.<br/>\n";
		$report .= "[>] installPath: " . $_SESSION['iInstallPath'] . "<br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	// to do with database
	//----------------------------------------------------------------------------------------------

	if (trim($_SESSION['iDbName']) == '') 
		{ $report .= "[*] Database name must not be blank.<br/>\n"; }

	$connect = @mysql_pconnect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass']);
	//$connect = mysql_connect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass']);

	if ($connect == false) {
		$report .= "[*] Could not establish persistent connection to database.<br/>\n";
		$report .= ""
				. "[>] dbHost: " . $_SESSION['iDbHost'] . " dbUser: " . $_SESSION['iDbUser'] 
				. "<br/>\n"
				. "[i] This may be because the connection settings are incorrect or "
				. "persistent connections are not enabled for your php.ini/mysql. <br/>\n"
				. "[i] See <a href='http://www.php.net/manual/en/features." 
				. "persistent-connections.php'>the php documentation</a> for more " 
				. "information on persistent connections (mysql_pconnect).<br/>";

	} else {
		$report .= "[|] connected to database server... OK<br/>\n";
	}
	
	$db = @mysql_select_db($_SESSION['iDbName'], $connect); 	
	if ($db == false) {
		$report .= "[*] Could not connect to database.<br/>";
		$report .= "[>] dbName: " . $_SESSION['iDbName'] . "<br/>";
		$report .= "[i] Please confirm database exists.<br/>";
	} else {
		$report .= "[|] established connection to database ... OK<br/>\n";
	}

	return $report;
}

//--------------------------------------------------------------------------------------------------
//	page header
//--------------------------------------------------------------------------------------------------

function printHeader() {
	echo "<html>\n"
		. "<title>Install Awarenet 3b</title>\n"
		. "<body>\n";

}

//--------------------------------------------------------------------------------------------------
//	page footer
//--------------------------------------------------------------------------------------------------

function printFooter() {
	echo "</body></html>";
}

//--------------------------------------------------------------------------------------------------
//	essential info form
//--------------------------------------------------------------------------------------------------

function showInstallForm() {
	echo "
<h1>new kapenta installation</h1>

<p>Welcome to Kapenta!  Before you begin, we'll need to complete a few settings, install some database tables
and create the administrator account.  If you're unsure of any of these settings, try contact your hosting 
provider or sysadmin.</p><br/>

<p><b>Please ensure that mod_rewrite is enabled before installing.</b></p>

<form name='install' method='POST'>
<input type='hidden' name='action' value='install' />

<h2>install path and server path</h2>
<p>Please enter the directory (document root) in which Kapenta is installed.  This is where the installation will
find its files, for example:
/var/www/kapenta/ or c:/webserver/kapenta/ (note trailing slash)</p>

<table noborder>
  <tr>
	<td width='100'><b>install path</b></td>
	<td><input type='text' name='iInstallPath' value='" . $_SESSION['iInstallPath'] . "' size='30' /></td>
  </tr>
</table>

<p>Please enter this installations address on the web, for constructing hyperlinks.  For example:
http://testserver/ or http://www.mydomain.com/ (note trailing slash)</p>

<table noborder>
  <tr>
	<td width='100'><b>server path</b></td>
	<td><input type='text' name='iServerPath' value='" . $_SESSION['iServerPath'] . "' size='30' /></td>
  </tr>
</table><br/>

<h2>database settings</h2>
<p>We'll need access to a mysql database, it's name, the ip/hostname of the database server, and a user
account to access it with.</p>

<table noborder>
  <tr>
	<td width='100'><b>db name</b></td>
	<td><input type='text' name='iDbName' value='" . $_SESSION['iDbName'] . "' size='30' /></td>
  </tr>
  <tr>
	<td width='100'><b>db host</b></td>
	<td><input type='text' name='iDbHost' value='" . $_SESSION['iDbHost'] . "' size='30' /></td>
  </tr>
  <tr>
	<td width='100'><b>db username</b></td>
	<td><input type='text' name='iDbUser' value='" . $_SESSION['iDbUser'] . "' size='30' /></td>
  </tr>
  <tr>
	<td width='100'><b>db password</b></td>
	<td><input type='password' name='iDbPass' value='" . $_SESSION['iDbPass'] . "' size='30' /></td>
  </tr>
</table><br/>

<h2>administrator account</h2>
<p>This account has complete control over your site, so pick a strong password: ie, not a dictionary word, date or 
the same password you use for a dozen other sites.</p>

<table noborder>
  <tr>
	<td width='100'><b>username</b></td>
	<td><input type='text' name='iAUser' value='" . $_SESSION['iAUser'] . "' size='30' /></td>
  </tr>
  <tr>
	<td width='100'><b>password (1)</b></td>
	<td><input type='password' name='iAPass1' value='" . $_SESSION['iAPass1'] . "' size='30' /></td>
  </tr>
  <tr>
	<td width='100'><b>password (2)</b></td>
	<td><input type='password' name='iAPass2' value='" . $_SESSION['iAPass2'] . "' size='30' /></td>
  </tr>
</table><br/>

<h2>all done?</h2>
<p>great!</p>
<input type='submit' value='Install Awarenet &gt;&gt;' />
</form>";
}

function saveSetupIncPhp($fileName) {
	$txt = "<" . "?" . "\n

//--------------------------------------------------------------------------------------------------
//    this is the main setup file for configuring your project
//--------------------------------------------------------------------------------------------------

//  (1) INSTALL PATH
//  This is how the project knows where it is on the system, for constructing absolute local 
//	filesystem paths.

    \$installPath = '" . $_SESSION['iInstallPath'] . "';

//  (2) SERVER PATH
//  Tells the project where it is on the net, set to '/' if there is any doubt, or if more than 
//	one domain name will be used.

    \$serverPath = '" . $_SESSION['iServerPath'] . "';

//  (3) DATABASE USER
//  This is the account through which the project will access the database.

    \$dbUser = '" . $_SESSION['iDbUser'] . "';
    \$dbPass = '" . $_SESSION['iDbPass'] . "';
    \$dbHost = '" . $_SESSION['iDbHost'] . "';
    \$dbName = '" . $_SESSION['iDbName'] . "';

//  (4) Default Module
//  This is the module which handles '/' requests
    
    \$defaultModule = 'static';

//  (5) BLOCK CACHE
//  Enable block cache

    \$useBlockCache = 'false';

//  (6) SITE NAME
//  Website name - for page titles

    \$websiteName = 'awareNet';

//  (7) THEME
//  Which of the installed themes the site is currently using.

    \$defaultTheme = 'clockface';

//  (8) LOG LEVEL
//  Log website activity to the specified level (0 - none, 1 - web requests, 2 - debug)

    \$logLevel = '2';
    
?" . ">";

	$fH = fopen($fileName, 'w+');
	if ($fH == false) { return false; }
	fwrite($fH, $txt);
	fclose($fH);
	return true;
}

//--------------------------------------------------------------------------------------------------
// 	determines if a file/dir exists and is readable + writable
//--------------------------------------------------------------------------------------------------

function is_extantrw($fileName) {
	if (file_exists($fileName)) {
		if (is_readable($fileName) == false) { return false; }
		if (is_writable($fileName) == false) { return false; }
	} else { return false; }
	return true;
}

//==================================================================================================
//	database functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// make default tables and records
//--------------------------------------------------------------------------------------------------

function makeDefaultTables() {

	//----------------------------------------------------------------------------------------------
	//	changes (revision) table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'changes';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'refTable' => 'VARCHAR(100)',
		'refUID' => 'VARCHAR(30)',	
		'data' => 'TEXT',
		'changedOn' => 'DATETIME',	
		'changedBy' => 'VARCHAR(30)' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'refTable' => '20', 
		'refUID' => '10' );

	// changes to records in the changes table are not to be stored (for obvious reason)
	$dbSchema['nodiff'] = array('UID', 'refTable', 'refUID', 'data', 'changedOn', 'changedBy');
	idbCreateTable($dbSchema);
	
	//----------------------------------------------------------------------------------------------
	//	migrated (static URL redirects)
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'migrated';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'fromUrl' => 'VARCHAR(255)',		
		'toUrl' => 'varchar(255)',
		'hitCount' => 'BIGINT' );

	$dbSchema['indices'] = array('UID' => '10', 'fromUrl' => '30');
	$dbSchema['nodiff'] = array('UID', 'fromURL', 'toURL', 'hitCount');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	recordalias table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'recordalias';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'refTable' => 'VARCHAR(100)',
		'refUID' => 'VARCHAR(30)',	
		'aliaslc' => 'VARCHAR(255)',
		'alias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'refTable' => '20', 'refUID' => '10', 'aliaslc' => '30');
	// no need to record changes to this table
	$dbSchema['nodiff'] = array('UID', 'refTable', 'refUID', 'aliaslc', 'alias');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => 'defaultra1',		
		'refTable' => 'schools',
		'refUID' => 'firstschool',	
		'aliaslc' => 'first-school',
		'alias' => 'First-School' );
		idbSave($data, $dbSchema);

	$data = array(
		'UID' => 'defaultra2',		
		'refTable' => 'users',
		'refUID' => 'admin',	
		'aliaslc' => 'admin',
		'alias' => 'Admin' );
		idbSave($data, $dbSchema);

	$data = array(
		'UID' => 'defaultra3',		
		'refTable' => 'users',
		'refUID' => 'public',	
		'aliaslc' => 'public',
		'alias' => 'Public' );
		idbSave($data, $dbSchema);

	//----------------------------------------------------------------------------------------------
	//	users table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'users';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'ofGroup' => 'VARCHAR(10)',
		'school' => 'VARCHAR(30)',
		'grade' => 'VARCHAR(30)',
		'firstname' => 'VARCHAR(100)',	
		'surname' => 'VARCHAR(100)',
		'username' => 'VARCHAR(30)',	
		'password' => 'VARCHAR(255)',
		'lang' => 'VARCHAR(30)',	
		'profile' => 'TEXT',
		'permissions' => 'TEXT',	
		'lastOnline' => 'DATETIME',
		'createdOn' => 'DATETIME',	
		'createdBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'lastOnline', 'recordAlias', 'password');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => 'admin',		
		'ofGroup' => 'admin',
		'school' => 'firstschool',
		'grade' => 'Std. 12',
		'firstname' => 'System',	
		'surname' => 'Administrator',
		'username' => $_SESSION['iAUser'],	
		'password' => sha1($_SESSION['iAPass1'] . 'admin'),
		'lang' => 'en',	
		'profile' => '',
		'permissions' => '',	
		'lastOnline' => imysql_datetime(),
		'createdOn' => imysql_datetime(),	
		'createdBy' => 'admin',
		'recordAlias' => 'Admin' );
		idbSave($data, $dbSchema);

	$data = array(
		'UID' => 'public',		
		'ofGroup' => 'public',
		'school' => 'firstschool',
		'grade' => 'Std. 1',
		'firstname' => 'Guest',	
		'surname' => '',
		'username' => 'public',	
		'password' => '',
		'lang' => 'en',	
		'profile' => '',
		'permissions' => '',	
		'lastOnline' => imysql_datetime(),
		'createdOn' => imysql_datetime(),	
		'createdBy' => 'admin',
		'recordAlias' => 'Public' );
		idbSave($data, $dbSchema);

	//----------------------------------------------------------------------------------------------
	//	school announcements
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'announcements';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'refModule' => 'VARCHAR(50)',
		'refUID' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'notifications' => 'VARCHAR(10)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	blog posts table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'blog';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'title' => 'VARCHAR(255)',
		'category' => 'VARCHAR(100)',	
		'content' => 'TEXT',
		'notes' => 'TEXT',	
		'tags' => 'TEXT',
		'createdOn' => 'DATETIME',	
		'createdBy' => 'VARCHAR(30)',
		'published' => 'VARCHAR(30)',
		'hitcount' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20', 'category' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	book pages
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'book';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'parent' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'weight' => 'BIGINT', 
		'comments' => 'VARCHAR(10)', 
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'parent' => 10, 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	calendar entries
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'calendar';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'title' => 'VARCHAR(255)',
		'category' => 'VARCHAR(100)',	
		'venue' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'year' => 'VARCHAR(5)',
		'month' => 'VARCHAR(5)',
		'day' => 'VARCHAR(5)',
		'eventStart' => 'VARCHAR(50)',
		'eventEnd' => 'VARCHAR(50)',
		'createdOn' => 'DATETIME',	
		'createdBy' => 'VARCHAR(30)',
		'published' => 'VARCHAR(30)',
		'hitcount' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20', 'category' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	chat message queue
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'chat';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'user' => 'VARCHAR(30)',
		'queue' => 'TEXT' );

	$dbSchema['indices'] = array('UID' => '10', 'user' => '10');
	$dbSchema['nodiff'] = array('UID', 'user', 'queue');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	comments table
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'comments';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'refModule' => 'VARCHAR(50)',
		'refUID' => 'VARCHAR(30)',
		'comment' => 'TEXT',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	files
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'files';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'refUID' => 'VARCHAR(30)',
		'refModule' => 'VARCHAR(30)',			
		'title' => 'VARCHAR(255)',
		'licence' => 'VARCHAR(100)',
		'attribName' => 'VARCHAR(255)',
		'attribURL' => 'VARCHAR(255)',
		'fileName' => 'VARCHAR(255)',
		'format' => 'VARCHAR(255)',
		'transforms' => 'TEXT',
		'caption' => 'TEXT',
		'category' => 'VARCHAR(100)',
		'weight' => 'VARCHAR(10)',
		'createdOn' => 'DATETIME',
		'createdBy' => 'VARCHAR(30)',
		'hitcount' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'refUID' => '10',
		'refModule' => '10',  
		'recordAlias' => '20', 
		'category' => '20' );

	$dbSchema['nodiff'] = array('UID', 'recordAlias', 'hitcount', 'transforms');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	folders (collections of userfiles)
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'folders';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'parent' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'parent' => 10, 
		'createdBy' => '10', 
		'recordAlias' => '20' );

	$dbSchema['nodiff'] = array('UID', 'recordAlias', 'children', 'files');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	forums
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'forums';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'school' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'weight' => 'VARCHAR(10)',
		'moderators' => 'TEXT',
		'members' => 'TEXT',
		'banned' => 'TEXT',
		'threads' => 'VARCHAR(30)',
		'replies' => 'VARCHAR(30)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'school' => 10, 'recordAlias' => '20' );
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	forum threads
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'forumthreads';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'forum' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'replies' => 'VARCHAR(10)',
		'sticky' => 'VARCHAR(10)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'updated' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 
								'forum' => 10, 
								'recordAlias' => '20', 
								'updated' => '' );

	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	forum replies
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'forumreplies';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'forum' => 'VARCHAR(30)',
		'thread' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 
								'forum' => 10,
								'thread' => 10,  
									);

	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	records of friendships between users
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'friendships';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'userUID' => 'VARCHAR(30)',
		'friendUID' => 'VARCHAR(30)',
		'relationship' => 'VARCHAR(100)',
		'status' => 'VARCHAR(255)',		
		'createdOn' => 'DATETIME' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'userUID' => '10', 
		'friendUID' => '10', 
		'status' => '5');

	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	gallery tables (collections of user images)
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'gallery';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'parent' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'parent' => 10, 'recordAlias' => '20' );
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	group memberships
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'groupmembers';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'groupUID' => 'VARCHAR(30)',	
		'userUID' => 'VARCHAR(30)',
		'position' => 'VARCHAR(20)',
		'admin' => 'VARCHAR(10)',
		'joined' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 'group' => '10', 'user' => '10');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	groups (like clubs, sports teams, etc
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'groups';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'school' => 'VARCHAR(30)',	
		'name' => 'VARCHAR(255)',
		'type' => 'VARCHAR(20)',
		'description' => 'TEXT',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	images
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'images';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'refUID' => 'VARCHAR(30)',
		'refModule' => 'VARCHAR(30)',			
		'title' => 'VARCHAR(255)',
		'licence' => 'VARCHAR(100)',
		'attribName' => 'VARCHAR(255)',
		'attribURL' => 'VARCHAR(255)',
		'fileName' => 'VARCHAR(255)',
		'format' => 'VARCHAR(255)',
		'transforms' => 'TEXT',
		'caption' => 'TEXT',
		'category' => 'VARCHAR(100)',
		'weight' => 'VARCHAR(10)',
		'createdOn' => 'DATETIME',
		'createdBy' => 'VARCHAR(30)',
		'hitcount' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'refUID' => '10',
		'refModule' => '10',  
		'recordAlias' => '20', 
		'category' => '20' );

	$dbSchema['nodiff'] = array('UID', 'recordAlias', 'hitcount', 'transforms');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	messages (pm module)
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'messages';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'owner' => 'VARCHAR(30)',
		'folder' => 'VARCHAR(30)',
		'fromUID' => 'VARCHAR(30)',
		'toUID' => 'VARCHAR(30)',
		'cc' => 'TEXT',
		'title' => 'VARCHAR(255)',
		're' => 'VARCHAR(30)',
		'content' => 'TEXT',
		'status' => 'VARCHAR(10)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 'fromUID' => 10, 'toUID' => '10' );
	$dbSchema['nodiff'] = array('UID');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	moblog (co-syndicated user blogs)
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'moblog';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'school' => 'VARCHAR(30)',
		'grade' => 'VARCHAR(30)',
		'title' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'published' => 'VARCHAR(30)',
		'hitcount' => 'BIGINT',
		'createdOn' => 'DATETIME',	
		'createdBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array(
		'UID' => '10', 
		'school' => '10',
		'grade' => '6',  
		'recordAlias' => '20', 
		'school' => '20');

	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	user notifications
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'notices';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'user' => 'VARCHAR(30)',	
		'notices' => 'TEXT' );

	$dbSchema['indices'] = array('UID' => '10', 'user' => '20');
	$dbSchema['nodiff'] = array('UID', 'notices');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	project memberships
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'projectmembers';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'projectUID' => 'VARCHAR(30)',	
		'userUID' => 'VARCHAR(30)',
		'role' => 'VARCHAR(10)',
		'joined' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 'projectUID' => '10', 'userUID' => '10');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	user projects (something like wiki articles)
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'projects';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',	
		'title' => 'VARCHAR(255)',
		'abstract' => 'TEXT',
		'content' => 'TEXT',
		'status' => 'VARCHAR(255)',		
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'finishedOn' => 'DATETIME',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	schools
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'schools';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'name' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'geocode' => 'TEXT',
		'country' => 'VARCHAR(255)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => 'firstschool',		
		'name' => 'First School',
		'description' => 'Describe your school here...',
		'geocode' => '',
		'country' => 'ZA',
		'recordAlias' => 'First-School' );

	idbSave($data, $dbSchema);

	//----------------------------------------------------------------------------------------------
	//	servers (sync module)
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'servers';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'servername' => 'VARCHAR(50)',
		'serverurl' => 'VARCHAR(100)',			
		'password' => 'VARCHAR(50)',
		'direction' => 'VARCHAR(50)',
		'active' => 'VARCHAR(10)'
	);

	$dbSchema['indices'] = array('UID' => '10');
	$dbSchema['nodiff'] = array('UID', 'password');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	static pages
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'static';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'title' => 'VARCHAR(255)',
		'menu1' => 'TEXT',
		'menu2' => 'TEXT',
		'content' => 'TEXT',	
		'nav1' => 'TEXT',
		'nav2' => 'TEXT',
		'script' => 'TEXT',
		'head' => 'TEXT',
		'createdOn' => 'DATETIME',	
		'createdBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	$fpc = "[[:theme::navtitlebox::width=570::label=Hello!:]][`|lt]h1[`|gt]Welcome to "
		 . "awareNet[`|lt]/h1[`|gt][`|lt]p[`|gt]awareNet is social networking software for "
		 . "schools, for creating student communities in a safe, rich environment that spans the "
		 . "digital divide.&nbsp[`|sc] It is free, open source software which anyone can use, "
		 . "change and redistribute.&nbsp[`|sc] This site is a live demo of an awareNet "
		 . "educational network, please sign up and play with it.[`|lt]br[`|gt][`|lt]/p[`|gt]"
		 . "[`|lt]h2[`|gt]eKhaya ICT[`|lt]/h2[`|gt]awareNet is developed by eKhaya ICT,&nbsp"
		 . "[`|sc] an IT project management and software development startup with a strong bond "
		 . "to South Africa[`|sq]s Eastern Cape and rural African communities."
		 . "[`|lt]br[`|gt][`|lt]br[`|gt]";

	$fpn = "[[:theme::navtitlebox::label=Log In:]]\n"
	 	 . "[[:users::loginform:]]\n[`|lt]br/[`|gt]\n[[:theme::navtitlebox::label=New Here?:]]"
		 . "[[:users::signupform::clear=yes:]]\n";

	$data = array(
		'UID' => 'frontpage',		
		'title' => 'Front Page',
		'menu1' => '[[:home::menu:]]',
		'menu2' => '',
		'content' => $fpc,	
		'nav1' => $fpn,
		'nav2' => '',
		'script' => '',
		'head' => '',
		'createdOn' => imysql_datetime(),	
		'createdBy' => 'admin',
		'recordAlias' => 'Front-Page' );
	idbSave($data, $dbSchema);	

	//----------------------------------------------------------------------------------------------
	//	wiki
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'wiki';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'title' => 'VARCHAR(255)',
		'content' => 'TEXT',
		'talk' => 'TEXT',
		'locked' => 'VARCHAR(20)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME',
		'hitcount' => 'BIGINT',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');

	$dbSchema['nodiff'] = array( 'UID', 'title', 'content', 'talk', 'locked', 'createdBy', 
								 'createdOn', 'editedBy', 'editedOn', 'hitcount', 
								 'recordAlias' );

	idbCreateTable($dbSchema);

	$data = array(
		'UID' => 'index',		
		'title' => 'Index',
		'content' => 'Index Goes here',
		'talk' => 'Talk Goes here',
		'locked' => 'admin',
		'createdBy' => 'admin',
		'createdOn' => imysql_datetime(),
		'editedBy' => 'admin',
		'editedOn' => imysql_datetime(),
		'hitcount' => '0',
		'recordAlias' => 'Index' );
	idbSave($data, $dbSchema);	

	//----------------------------------------------------------------------------------------------
	//	wikirevisions
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'wikirevisions';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'refUID' => 'VARCHAR(30)',		
		'content' => 'TEXT',
		'type' => 'VARCHAR(50)',
		'reason' => 'VARCHAR(255)',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10');

	$dbSchema['nodiff'] = array( 'UID', 'refUID', 'content', 'type', 'reason', 
								 'editedBy', 'editedOn', 'recordAlias' );

	idbCreateTable($dbSchema);
}

//--------------------------------------------------------------------------------------------------
// add the admin user, the public user and the admin users school
//--------------------------------------------------------------------------------------------------

function makeDefaultRecords() {
	//----------------------------------------------------------------------------------------------
	//	admin user
	//----------------------------------------------------------------------------------------------
	
}

//--------------------------------------------------------------------------------------------------
// make a table + indices
//--------------------------------------------------------------------------------------------------

function idbCreateTable($dbSchema) {
	//----------------------------------------------------------------------------------------------
	//	check if table already exists
	//----------------------------------------------------------------------------------------------
  	$result = idbQuery("SHOW TABLES FROM "  . $_SESSION['iDbName']);
	while ($row = mysql_fetch_row($result)) {
		if ($row[0] == $dbSchema['table']) {
			echo "[*] Table " . $dbSchema['table'] . " already exists.<br/>\n";
			return false;
		} 
	}

	echo "[>] Creating database table " . $dbSchema['table'] . "...<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	create table
	//----------------------------------------------------------------------------------------------
	$sql = "create table " . $dbSchema['table'] . " (\n";
	$fields = array();
	foreach($dbSchema['fields'] as $fieldName => $fieldType) {
		$fields[] = '  ' . $fieldName . ' ' . $fieldType;
	}
	$sql .= implode(",\n", $fields) . ");\n";

	idbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	indices
	//----------------------------------------------------------------------------------------------
	foreach($dbSchema['indices'] as $idxField => $idxSize) {
		$idxName = 'idx' . $dbSchema['table'] . $idxField;
		if ($idxSize == '') {
			$sql = "create index $idxName on " . $dbSchema['table'] . ";";
		} else {
			$sql = "create index $idxName on " . $dbSchema['table'] . " (" . $idxField . "(10));";
		}
		idbQuery($sql);
	}

}

//--------------------------------------------------------------------------------------------------
// execute a query, return handle
//--------------------------------------------------------------------------------------------------

function idbQuery($query) {
	// connect to database
	$connect = mysql_pconnect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass'])
			   or die("no connect");

	mysql_select_db($_SESSION['iDbName'], $connect); 

	$result = mysql_query($query, $connect) ;
			  //or die("<h1>Database Error... sorry :-(</h1>" . mysql_error() ."<p>" . $query);

	return $result;
}

//--------------------------------------------------------------------------------------------------
// save a record given a dbSchema array and an array of field values, returns false on failue
//--------------------------------------------------------------------------------------------------

function idbSave($data, $dbSchema) {
	if (array_key_exists('UID', $data) == false) { return false; }	
	if (strlen(trim($data['UID'])) < 4) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if the record already exists, take no action if it does
	//----------------------------------------------------------------------------------------------
	if (idbRecordExists($dbSchema['table'], $data['UID']) == true) { 
		echo '[*] Record ' . $data['UID'] ' already exists in table ' . $dbSchema['table']
			 . ", leaving as is.<br/>\n";
		return false; 
	}

	//----------------------------------------------------------------------------------------------
	//	delete the current record, if it exists (removed, failsafe)
	//----------------------------------------------------------------------------------------------
	//$sql = "delete from " . $dbSchema['table'] . " where UID='" . $data['UID'] . "'";
	//idbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	save a new one
	//----------------------------------------------------------------------------------------------

	$sql = "insert into " . $dbSchema['table'] . " values (";
	foreach ($dbSchema['fields'] as $fName => $fType) {
	  if (strlen($fName) > 0) {
		$quote = true;
		$value = ''; // . $fName . ':';

		//------------------------------------------------------------------------------------------
		//	some field types should be quotes, some not
		//------------------------------------------------------------------------------------------
		switch (strtolower($fType)) {
			case 'bigint': 		$quote = false; break;
			case 'tinyint';		$quote = false; break;
		}

		//------------------------------------------------------------------------------------------
		//	clean the value and add to array
		//------------------------------------------------------------------------------------------
		if (array_key_exists($fName, $data)) { $value = isqlMarkup($data[$fName]); } 
		if ($quote) { $value = "\"" . $value . "\""; }
		$sql .= $value . ',';
	   }
	}

	$sql = substr($sql, 0, strlen($sql) - 1);
	$sql .= ");";	
	idbQuery($sql);
}

//--------------------------------------------------------------------------------------------------
// 	get/convert the current date/time into mySQL format
//--------------------------------------------------------------------------------------------------

function imysql_dateTime() { return gmdate("Y-m-j H:i:s", time()); }
function imk_mysql_dateTime($date) { return gmdate("Y-m-j H:i:s", $date); }

//--------------------------------------------------------------------------------------------------
// 	sanitize a value before using it in a sql statement, to prevent SQL injection, some XSS, etc
//--------------------------------------------------------------------------------------------------

function isqlMarkup($text) {							// WHY?
	$text = str_replace('%', "[`|pc]", $text);			// wildcard characters in SQL
	$text = str_replace('_', "[`|us]", $text);			// ... 
	$text = str_replace(';', "[`|sc]", $text);			// used to construct SQL statements
	$text = str_replace("'", "[`|sq]", $text);			// ...
	$text = str_replace("\"", "[`|dq]", $text);			// ...
	$text = str_replace('<', "[`|lt]", $text);			// interference between nested XML schema
	$text = str_replace('>', "[`|gt]", $text);			// ...
	$text = str_replace("\t", "[`|tb]", $text);			// mysql errors
	$text = str_replace('select', "[`|select]", $text);	// SQL statements  
	$text = str_replace('delete', "[`|delete]", $text);	// ...
	$text = str_replace('create', "[`|create]", $text);	// ...
	$text = str_replace('insert', "[`|insert]", $text);	// ...
	$text = str_replace('update', "[`|update]", $text);	// ...
	$text = str_replace('drop', "[`|drop]", $text);		// ...
	$text = str_replace('table', "[`|table]", $text);	// ...
	return $text;
}

//--------------------------------------------------------------------------------------------------
// 	remove sql markup
//--------------------------------------------------------------------------------------------------

function isqlRemoveMarkup($text) {
	$text = str_replace("[`|pc]", '%', $text);
	$text = str_replace("[`|us]", '_', $text);
	$text = str_replace("[`|sc]", ';', $text);
	$text = str_replace("[`|sq]", "'", $text);
	$text = str_replace("[`|dq]", "\"", $text);
	$text = str_replace("[`|lt]", "<", $text);
	$text = str_replace("[`|gt]", ">", $text);
	$text = str_replace("[`|tb]", "\t", $text);
	$text = str_replace("[`|select]", 'select', $text);
	$text = str_replace("[`|delete]", 'delete', $text);
	$text = str_replace("[`|create]", 'create', $text);
	$text = str_replace("[`|insert]", 'insert', $text);
	$text = str_replace("[`|update]", 'update', $text);
	$text = str_replace("[`|drop]", 'drop', $text);
	$text = str_replace("[`|table]", 'table', $text);

	//----------------------------------------------------------------------------------------------
	// legacy markup, from kapenta 1, remove these if not migrating old data
	//----------------------------------------------------------------------------------------------

	$text = str_replace("[`|squote]", "'", $text);
	$text = str_replace("[`|quote]", "\"", $text);
	$text = str_replace("[`|semicolon]", ";", $text);

	return $text;
}

//--------------------------------------------------------------------------------------------------
// 	remove sql markup from an array (no nested arrays)
//--------------------------------------------------------------------------------------------------

function isqlRMArray($ary) {
	$retVal = array();
	foreach ($ary as $key => $val) {
		$retVal[$key] = isqlRemoveMarkup($val);
	}
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
// 	check if a record with given UID exists in a table
//--------------------------------------------------------------------------------------------------

function idbRecordExists($table, $UID) {
	$sql = "SELECT * FROM $table WHERE UID='" . sqlMarkup($UID) . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) == 0) { return false; }
	return true;
}

//==================================================================================================
//	code repository
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	download repository list and convert into an array
//--------------------------------------------------------------------------------------------------

function getRepositoryList($repository) {
	echo "[>] Downlading list of files...<br/>\n";
	$rList = array();
	$raw = implode(file($repository));	
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$cols = explode("\t", $line);
		$item = array(
			'UID' => $cols[0],
			'sha1' => $cols[1],
			'type' => $cols[2],
			'relfile' => $cols[3]
			);

		$rList[$item['UID']] = $item;
	}
	return $rList;
}

//--------------------------------------------------------------------------------------------------
// download any outstanding files, returns list of any which failed
//--------------------------------------------------------------------------------------------------

function downloadFromRepository($itemList, $repositoryDoor) {
	$rList = $itemList;
	$retryList = array();

	//----------------------------------------------------------------------------------------------
	//	create all folders
	//----------------------------------------------------------------------------------------------
	$folders = array();												// make list of folders
	foreach($rList as $UID => $item) 
		{ if ($item['type'] == 'folder') { $folders[] = $item['relfile']; }	}

	asort($folders);												// sort (ie, start from root)
	foreach($folders as $folder) {
			$folder = $_SESSION['iInstallPath'] . $folder;
			$folder = str_replace('//', '/', $folder);
			@mkdir($folder);										// it bitches about extant ones
			echo "[>] created directory $folder<br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	download everything that's not a folder
	//----------------------------------------------------------------------------------------------
	foreach($rList as $UID => $item) { 
		if ($item['type'] != 'folder') { 

			$outFile = $_SESSION['iInstallPath'] . $item['relfile'];
			$outFile = str_replace('//', '/', $outFile);

			if (file_exists($outFile) == true) {
				echo "[|] Skipping $outFile (already present)<br/>\n";

			} else {
				//----------------------------------------------------------------------------------
				//	download from repository door
				//----------------------------------------------------------------------------------
				$content = @file($repositoryDoor . $item['UID']);

				if ($content == false) {
					echo "[*] Error: could not download $outFile (UID:" . $item['UID'] . ")<br/>\n";	
					$retryList[$item['UID']] = $item;	// failed, retry

				} else {
					//------------------------------------------------------------------------------
					//	content is base64 encoded
					//------------------------------------------------------------------------------
					$content = base64_decode(implode($content));

					//------------------------------------------------------------------------------
					//	save it :-)
					//------------------------------------------------------------------------------
					
					$fH = fopen($outFile, 'w+');
					if ($fH == false) {
						echo "[*] Error: could not open $outFile for writing.<br/>\n";	flush();
						$retryList[$item['UID']] = $item;	// failed, retry

					} else {
						echo "[>] Saving $outFile (UID:" . $item['UID'] . ") "
							 . "(type:" . $item['type'] . ")<br/>\n";	flush();

						fwrite($fH, $content);
						fclose($fH);		

					} // end if cant write
				} // end if bad download
			} // end if $outFile exists
		}  // end if != folder
	}  // end foreach rlist

	return $retryList;
}

?>
