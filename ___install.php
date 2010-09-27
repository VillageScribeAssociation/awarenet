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
		{ $_SESSION['iInstallPath'] = dirname($_SERVER['SCRIPT_FILENAME']) . '/'; }

	// server path
	if (!(array_key_exists('iServerPath', $_SESSION))) { 
			$_SESSION['iServerPath'] = 'http://' . $_SERVER['HTTP_HOST']
									 . dirname($_SERVER['REQUEST_URI']); 
	}

	// http proxy
	if (!(array_key_exists('iProxyEnabled', $_SESSION))) { $_SESSION['iProxyEnabled'] = 'no'; }
	if (!(array_key_exists('iProxyAddress', $_SESSION))) { $_SESSION['iProxyAddress'] = ''; }
	if (!(array_key_exists('iProxyPort', $_SESSION))) { $_SESSION['iProxyPort'] = ''; }
	if (!(array_key_exists('iProxyUser', $_SESSION))) { $_SESSION['iProxyUser'] = ''; }
	if (!(array_key_exists('iProxyPass', $_SESSION))) { $_SESSION['iProxyPass'] = ''; }

	// database
	if (!(array_key_exists('iDbName', $_SESSION))) { $_SESSION['iDbName'] = ''; }
	if (!(array_key_exists('iDbHost', $_SESSION))) { $_SESSION['iDbHost'] = ''; }
	if (!(array_key_exists('iDbUser', $_SESSION))) { $_SESSION['iDbUser'] = ''; }
	if (!(array_key_exists('iDbPass', $_SESSION))) { $_SESSION['iDbPass'] = ''; }

	// UIDs
	if (!(array_key_exists('iSchoolUID', $_SESSION))) { $_SESSION['iSchoolUID'] = ''; }
	if (!(array_key_exists('iAdminUID', $_SESSION))) { $_SESSION['iAdminUID'] = 'admin'; }

//-------------------------------------------------------------------------------------------------
//	globals
//-------------------------------------------------------------------------------------------------

	$repositoryList = 'http://kapenta.org.uk/code/projectlist/project_106665425118526027';
	$repositoryDoor = 'http://kapenta.org.uk/code/projectfile/file_';

	$testsOk = false;
	$report = '';

	$maxRetries = 3;

	$page = pageInit();
	$installState = array();

//-------------------------------------------------------------------------------------------------
//	get POST vars from form
//-------------------------------------------------------------------------------------------------

	foreach($_SESSION as $key => $val) 
		{ if (array_key_exists($key, $_POST) == true) { $_SESSION[$key] = $_POST[$key]; } }

	getInstallState();			// check for existing installation

//-------------------------------------------------------------------------------------------------
//	discover what page has been requested of this script
//-------------------------------------------------------------------------------------------------

	if (array_key_exists('page', $_GET) == true) {
		switch ($_GET['page']) {
			case 'install.css':	echo embedInstallCss(); break;

			case 'start': pageLoadWelcome(); pageRender(); break;
			case 'location': pageLoadLocation(); pageRender(); break;
			case 'testmodrw': pageLoadTestModRW(); pageRender(); break;
			case 'testdb': pageLoadTestDb(); pageRender(); break;
			case 'makeadmin': pageLoadMakeAdmin(); pageRender(); break;
			case 'installdb': pageLoadInstallDb(); pageRender(); break;
			case 'makesetup': pageLoadMakeSetup(); pageRender(); break;
			case 'downloadcode': pageLoadDownloadCode(); pageRender(); break;
			case 'getrepository': pageLoadGetRepository(); break;

		}		

	} else {
		// show default page
		pageLoadWelcome();
		pageRender();
	}

//-------------------------------------------------------------------------------------------------
//	** end of install script **
//-------------------------------------------------------------------------------------------------

//=================================================================================================
//	FUNCTIONS TO RENDER PAGES
//=================================================================================================

function pageInit() { 
	return array(
		'template' => pageGetTemplate(), 
		'content' => '',
		'nav' => ''
	); 
}

//-------------------------------------------------------------------------------------------------
//	render the page
//-------------------------------------------------------------------------------------------------

function pageRender() {
	global $page;
	$page['template'] = str_replace('%%pageContent%%', $page['content'], $page['template']);
	$page['template'] = str_replace('%%pageNav%%', $page['nav'], $page['template']);
	$page['template'] = str_replace('%%sMessage%%', $_SESSION['sMessage'], $page['template']);
	$_SESSION['sMessage'] = '';
	echo $page['template'];
}

//-------------------------------------------------------------------------------------------------
//	get the default page template
//-------------------------------------------------------------------------------------------------

function pageGetTemplate() {
	$html = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>Install awareNet</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<style type='text/css'>
" . embedInstallCss() . "
.style1 {font-size: 9px}
</style>
</head>

<body> 

<center>
<table class='tableborder' cellspacing='0' cellpadding='0' height='100%' class='table_main' >

  <tr>
	<!-- white border -->
	<td width='40'></td>
	<!-- center column -->
	<td valign=top >

	<table border=0 cellspacing=0 cellpadding=0 valign='top' width='900'>

	  <tr>
	    <td width='900' height='41' background='http://awarenet.eu/themes/clockface/images/menuTop.png'>
			

			<div style='float:left; width:180px;  margin-top: 0px; margin-left:20px;'>
				<a href='http://awarenet.eu/'>
				<img src='http://awarenet.eu/themes/clockface/images/awareNetLogo.png' border='0' />
				</a>
			</div>

			<div style='margin-right:60px; margin-top:0px; text-align:right'>
				<span style='vertical-align: bottom; background-color: black;'>
				</span>
			</div>

		</td>
	  </tr>

	  <tr>
	    <td width='900' height='28' background='http://awarenet.eu/themes/clockface/images/menuBottom.png'>
			<small>&nbsp;</small>
			
		</td>
	  </tr>	
	  <tr><td><br/></td></tr>

	  <tr>
	    <td></td>
	  </tr>	

	  <tr>
	    <td>
	
		<table border='0' width='100%' cellpadding='0' cellspacing='0' valign='top'>
		  <tr>

		    <td valign='top' align='left'>
				%%sMessage%%
				<br/>
				%%pageContent%%				

		    </td>

		    <td valign='top' align='left' width='30'></td>

		    <td valign='top' align='left' width='300'>
			<br/>
			%%pageNav%%
			<br/>
			
		    </td>						
		  </tr>
		</table>

	    </td>
	  </tr>	
	  <tr>
			<td></td>
	  </tr>
	</table>

	<br/><br/>
    </td>
	<!-- white border -->
	<td width='40'></td>
  </tr>	
  <tr>
	<!-- white border -->
	<td width='40'></td>
	<!-- footer -->

    <td bgcolor='#333333' height='22' align='center'>
		<small><span style='color: #eee;'>awareNet is developed by <a href='http://eckayaICT.com' class='menu'>eKhaya ICT</a>
				building bridges across the digital divide.</span>
		</small>
    </td>
	<!-- white border -->
	<td width='40'></td>

  </tr>	
</table>
<br/><br/><br/>
</center>
</body>
</html>";

	return $html;

}

//-------------------------------------------------------------------------------------------------
//	initial default welcome page
//-------------------------------------------------------------------------------------------------

function pageLoadWelcome() {
	global $page;

	$report = '';

	//---------------------------------------------------------------------------------------------
	//	check for required extensions
	//---------------------------------------------------------------------------------------------

	if (function_exists('mysql_query') == false) {
		$report .= "<p><span class='ajaxerror'>MySQL not installed</span> Please install the PHP 
					MySQL extension, awareNet can not work without it.</p>";
	} else {
		$report .= "<p><span class='ajaxmsg'>MySQL installed</span> MySQL extension is installed
					 correctly.</p>";
	}

	if (function_exists('curl_init') == false) {
		$report .= "<p><span class='ajaxerror'>cURL not installed</span> Please install the PHP 
					cURL extension, awareNet can not work without it.</p>";
	} else {
		$report .= "<p><span class='ajaxmsg'>cURL installed</span> cURL is installed correctly.</p>";
	}

	if (function_exists('imagecreatetruecolor') == false) {
		$report .= "<p><span class='ajaxerror'>gd not installed</span> Please install the PHP 
					GD/GD2 image library before you proceed.</p>";
	} else {
		$report .= "<p><span class='ajaxmsg'>GD2 installed</span> image library installed 
					correctly.</p>";
	}

	//---------------------------------------------------------------------------------------------
	//	show the page
	//---------------------------------------------------------------------------------------------

	$page['content'] = "
		<div class='navbox' >Welcome</div>
		<h1>awareNet Install</h1>
		<p>This script will install the <a href='http://awarenet.eu/'>awareNet</a> suite of social
		networking software on this web server.  If you can read this page you already have php
		installed correctly, the following pages will check system requirements and configure your
		instance of the software.</p>

		$report
		<hr>

		<form name='testFilePerms' method='GET'>
		<input type='hidden' name='page' value='location' />
		<input type='submit' value='Step 1: Location &gt;&gt;' />
		</form>
	";

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";

}

//-------------------------------------------------------------------------------------------------
//	ask for serverPath and installpath
//-------------------------------------------------------------------------------------------------

function pageLoadLocation() {
	global $page;

	$nextButton = "
			<td>
				<form name='testFilePerms' method='GET'>
				<input type='hidden' name='page' value='testmodrw' />
				<input type='submit' value='Step 2: Check Mod Rewrite &gt;&gt;' />
				</form>
			</td>
		";
	
	if (fileWriteHTA() == true) {
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		<p><span class='ajaxmsg'>These settings seem to work.</span></p>
		";
	} else { 
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		<p><span class='ajaxerror'>These settings don't work, either the installPath is incorrect, or the path exists 
		but cannot be written to.</span></p>
		";
		$nextButton = ''; 
	}

	$page['content'] = "
		<div class='navbox'>Locations</div>
		<h1>awareNet Install</h1>
		<p>Your awareNet installation will need to know where it is installed on the web server
		and where it can be found on the network.</p>

		<hr/>

		<form name='install' method='POST' action='install.php?page=location'>
		<input type='hidden' name='action' value='install' />

		<p>Please enter the directory (document root) in which awareNet is to be installed.  This
		is where the installation will find its files, for example:
		/var/www/awareNet/ or c:/webserver/awareNet/ (note trailing slash)</p>

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

		<p>If this installation is behind a web proxy the installer will need its details in order to download
		software from the repository, configure automatic updates and use awareNet's peer-to-peer features.</p>

		<table noborder>
		  <tr>
			<td><b>use proxy</b></td>
			<td>
			  <select name='iProxyEnabled'>
			    <option value='" . $_SESSION['iProxyEnabled'] . "'>" . $_SESSION['iProxyEnabled'] . "</option>
			    <option value='no'>no</option>
			    <option value='yes'>yes</option>
			  </select>
			</td>
		  </tr>
		  <tr>
			<td><b>proxy address</b></td>
			<td><input type='text' name='iProxyAddress' value='" . $_SESSION['iProxyAddress'] . "' size='30' /></td>
		  </tr>
		  <tr>
			<td><b>proxy port</b></td>
			<td><input type='text' name='iProxyPort' value='" . $_SESSION['iProxyPort'] . "' size='30' /></td>
		  </tr>
		  <tr>
			<td><b>proxy user</b></td>
			<td><input type='text' name='iProxyUser' value='" . $_SESSION['iProxyUser'] . "' size='30' /></td>
		  </tr>
		  <tr>
			<td><b>proxy password &nbsp;</b></td>
			<td><input type='password' name='iProxyPass' value='" . $_SESSION['iProxyPass'] . "' size='30' /></td>
		  </tr>
		</table>
		<br/>
		<input type='submit' value='Test These Settings &gt;&gt;' />
		</form>
		<br/>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='start' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			%%nextButton%%
		  </tr>
		</table>
	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	
}

//-------------------------------------------------------------------------------------------------
//	test write permissions
//-------------------------------------------------------------------------------------------------

function pageLoadTestModRW() {
	global $page;

	$pass = apacheModuleInstalled('mod_rewrite');

	if (true == $pass) {
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		<p><span class='ajaxmsg'>Apache mod_rewrite is installed.</span></p>
		";		
	} else {
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		<p><span class='ajaxerror'>Apache mod_rewrite is not installed, please contact your hosting provider
		to resolve this.</span></p>
		";
	}

	$page['content'] = "
		<div class='navbox'>Mod Rewrite</div>
		<h1>awareNet Install</h1>
		<p>awareNet uses virtual URLs and typically uses  
		<a href='http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html'>Apache's URL Rewriting 
		Engine</a> to convert them.  If you're using a web server other than Apache then you may
		still be able to install awareNet if you can configure an equivalent URL rewriter; please 
		<a href='mailto:awarenetdev@gmail.com'>contact us</a> for ZEND support.</p>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='location' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			<td>
				<form name='testMonRewrite' method='GET'>
				<input type='hidden' name='page' value='testdb' />
				<input type='submit' value='Step 3: Configure Database &gt;&gt;' />
				</form>
			</td>
		  </tr>
		</table>

	";

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	
}

//-------------------------------------------------------------------------------------------------
//	test database settings
//-------------------------------------------------------------------------------------------------

function pageLoadTestDb() {
	global $page;

	$nextButton = "
			<td>
				<form name='testFilePerms' method='GET'>
				<input type='hidden' name='page' value='makeadmin' />
				<input type='submit' value='Step 4: Create Administrator Account &gt;&gt;' />
				</form>
			</td>
		";
	
	$report = dbTest();
	$pass = false;
	if (strpos($report, 'ajaxmsg') > 0) { $pass = true; }

	if (true == $pass) {
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		$report
		<p><span class='ajaxmsg'>These settings seem to work.</span></p>
		";
	} else { 
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		$report
		<p><span class='ajaxerror'>These settings don't work.</span></p>
		";
		$nextButton = ''; 
	}

	$page['content'] = "
		<div class='navbox'>MySQL Databse</div>
		<h1>awareNet Install</h1>
		<p>awarenet stores its information in a <a href='http://www.mysql.org'>MySQL</a> database.  
		To access the database  we'll need it's name, the ip/hostname of the database server, and a
		database user account with permissions to access and modify it.</p>

		<hr/>

		<form name='install' method='POST' action='install.php?page=testdb'>
		<input type='hidden' name='action' value='install' />

		<h2>database settings</h2>

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

		<input type='submit' value='Test Database Settings &gt;&gt;' />
		</form>
		<br/>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='testmodrw' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			%%nextButton%%
		  </tr>
		</table>
	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	
}

//-------------------------------------------------------------------------------------------------
//	get admin account details
//-------------------------------------------------------------------------------------------------

function pageLoadMakeAdmin() {
	global $page;
	global $installState;

	//---------------------------------------------------------------------------------------------
	// check current settings
	//---------------------------------------------------------------------------------------------

	$report = '';

	if ($_SESSION['iAPass1'] != $_SESSION['iAPass2']) 
		{ $report .= "[*] Passwords do not match.<br/>\n"; }

	if (strlen(trim($_SESSION['iAPass1'])) < 4)
		{ $report .= "[*] Please choose a password of more than four characters.<br/>\n"; }

	if (strlen($_SESSION['iAUser']) < 4) 
		{ $report .= "[*] Administrator user name should be at least four chars.<br/>\n"; }

	//---------------------------------------------------------------------------------------------
	// check if admin already exists
	//---------------------------------------------------------------------------------------------

	if (true == $installState['adminUser']) { $report = ''; }

	$nextButton = "
			<td>
				<form name='testFilePerms' method='GET'>
				<input type='hidden' name='page' value='installdb' />
				<input type='submit' value='Step 4: Create Database Tables &gt;&gt;' />
				</form>
			</td>
		";

	//---------------------------------------------------------------------------------------------
	// test results and 'next page' button
	//---------------------------------------------------------------------------------------------
	
	if ('' == $report) {
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		<p>Please keep these details secure.</p>
		<p><span class='ajaxmsg'>pass</span></p>
		";
	} else { 
		$_SESSION['sMessage'] .= "
		<br/>
		<div class='navbox'>Test Result</div>
		$report
		<p><span class='ajaxerror'>Please complete the form below.</span></p>
		";
		$nextButton = ''; 
	}

	//---------------------------------------------------------------------------------------------
	// form for setting admin user details
	//---------------------------------------------------------------------------------------------

	$adminForm = "
		<p>Please create an account for the system administrator.  This account has complete 
		control over your awareNet, so pick a strong password: ie, not a dictionary word, date 
		or the same password you use on other sites.</p>

		<hr/>

		<form name='install' method='POST' action='install.php?page=makeadmin'>
		<input type='hidden' name='action' value='install' />

		<h2>administrator account</h2>

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
		  <tr>
			<td width='100'><b>UID</b></td>
			<td>" . $_SESSION['iAdminUID'] . "</td>
		  </tr>
		</table><br/>

		<input type='submit' value='Save Account Settings &gt;&gt;' />
		</form>
	";

	if (true == $installState['adminUser']) { 
		$adminForm = "<div class='ajaxmsg'>An administrator account already exists on this "
				   . "installation.  You do not need to perform this step.</div>";
	}

	//---------------------------------------------------------------------------------------------
	// put it all together
	//---------------------------------------------------------------------------------------------

	$page['content'] = "
		<div class='navbox'>Administrator Account</div>
		<h1>awareNet Install</h1>
		$adminForm
		<br/>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='testdb' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			%%nextButton%%
		  </tr>
		</table>
	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	
}

//-------------------------------------------------------------------------------------------------
//	get admin account details
//-------------------------------------------------------------------------------------------------

function pageLoadInstallDb() {
	global $page;

	//---------------------------------------------------------------------------------------------
	// make default tables
	//---------------------------------------------------------------------------------------------

	makeDefaultTables();

	//---------------------------------------------------------------------------------------------
	// display tables
	//---------------------------------------------------------------------------------------------

	$tables = i$db->loadTables();
	$tableList = '';
	foreach($tables as $table) {
		$dbSchema = idbGetSchema($table);
		$tableList .= idbSchemaToHtml($dbSchema);
	}

	//---------------------------------------------------------------------------------------------
	// render the page
	//---------------------------------------------------------------------------------------------

	$page['content'] = "
		<div class='navbox'>Database Setup</div>
		<h1>awareNet Install</h1>
		<p>awareNet has installed the following tables for use with default modules.  
		Automated updates may modify the database structure from time to time.</p>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='testdb' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			<td>
				<form name='testFilePerms' method='GET'>
				<input type='hidden' name='page' value='makesetup' />
				<input type='submit' value='Step 5: Create Configuration Files &gt;&gt;' />
				</form>
			</td>
		  </tr>
		</table>

		<hr/>

		<h2>FYI: Database Structure</h2>

		$tableList
	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	

}

function pageLoadMakeSetup() {
	global $page;

	//---------------------------------------------------------------------------------------------
	// make default tables
	//---------------------------------------------------------------------------------------------

	$fileName = $_SESSION['iInstallPath'] . 'setup.inc.php';
	saveSetupIncPhp($fileName);
	$pass = true;
	$setupData = '';

	if (file_exists($fileName) == true) {
		$setupData = implode(file($fileName));
		//$setupData = str_replace("\n", "<br/>\n", $setupData);
		$setupData = str_replace("<", "&lt", $setupData);
		$setupData = str_replace(">", "&gt", $setupData);

	} else {
		$pass = false;
	}

	//---------------------------------------------------------------------------------------------
	// render the page
	//---------------------------------------------------------------------------------------------

	$page['content'] = "
		<div class='navbox'>Site Confguration File</div>
		<h1>awareNet Install</h1>
		<p>The following site configuration file has been saved:</p>
		<b>$fileName</b><br/><br/>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='installdb' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			<td>
				<form name='testFilePerms' method='GET'>
				<input type='hidden' name='page' value='downloadcode' />
				<input type='submit' value='Step 5: Install Core, Modules and Theme &gt;&gt;' />
				</form>
			</td>
		  </tr>
		</table>

		<hr/>

		<h2>FYI: Site Configuration</h2>

		<pre><small>$setupData</small></pre>		
	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	

}

function pageLoadDownloadCode() {
	global $page;

	//---------------------------------------------------------------------------------------------
	// render the page
	//---------------------------------------------------------------------------------------------

	$page['content'] = "
		<div class='navbox'>Installing Core, Modules and Theme</div>
		<h1>awareNet Install</h1>
		<p>Please wait while components are downloaded:</p>

		<iframe id='ifRepo' src='install.php?page=getrepository' width='570' height='300' frameborder='no'></iframe>

		<hr>
		<table noborder>
		  <tr>
		    <td>
				<form name='goback' method='GET'>
				<input type='hidden' name='page' value='makesetup' />
				<input type='submit' value='&lt;&lt; Back' />
				</form>
			</td>
			<td>
				<div id='nextButton' style='visibility: hidden; display: none;'>
				<form name='testFilePerms' method='POST' action='" . $_SESSION['iServerPath'] . "'>
				<input type='hidden' name='page' value='downloadcode' />
				<input type='submit' value='Finished!  &gt;&gt;' />
				</form>
				</div>
			</td>
		  </tr>
		</table>

		<hr/>

		<script language='javascript'>
			var stopScrolling = false;

			function showNextButton() {
				var theDiv = document.getElementById('nextButton');
				theDiv.style.visibility = 'visible';
				theDiv.style.display = 'block';
				stopScrolling = true;
			}

			function scrollIf() {
				var theIf = document.getElementById('ifRepo');
				theIf.contentWindow.document.body.scrollTop = theIf.contentWindow.document.body.scrollHeight;
				if (false == stopScrolling) { setTimeout('scrollIf();', 300); }
			}

			scrollIf();

		</script>

	";

	$page['content'] = str_replace('%%nextButton%%', $nextButton, $page['content']);

	$page['nav'] = "
		<div class='navbox' >Important</div>
		<p>Once you have completed this process you should delete the install.php file as soon as 
		possible.  Leaving it on your system may allow others to gain control of it.</p>
		";	

}

function pageLoadGetRepository() {
	global $maxRetries;
	global $repositoryList;
	global $repositoryDoor;

	echo "<html>
		<style type='text/css'>
		" . embedInstallCss() . "
		.style1 {font-size: 9px}
		</style>
		<body><small>\n";

	//echo "<h2>This installer does not use the repository.  You're done :-)</h2>";

	//---------------------------------------------------------------------------------------------
	// download all files from repository
	//---------------------------------------------------------------------------------------------

	echo "<h2>Downloading from repository...</h2>\n";		
	$itemList = getRepositoryList($repositoryList);						// get list of items
	$retryList = downloadFromRepository($itemList, $repositoryDoor);	// download each item

	//---------------------------------------------------------------------------------------------
	// retry any files which failed
	//---------------------------------------------------------------------------------------------

	for ($i = 0; $i < $maxRetries; $i++) {
		if (count($retryList) > 0) {
			echo "<h1>Retrying... ($i) </h1>\n";
			$retryList = downloadFromRepository($retryList, $repositoryDoor);		
		}
	}

	//-----------------------------------------------------------------------------------------
	// print list of any files which failed after three attempts
	//-----------------------------------------------------------------------------------------
	// TODO

	//-----------------------------------------------------------------------------------------
	// 302 to home page
	//-----------------------------------------------------------------------------------------
	echo "<br/><br/><a href='" . $_SESSION['iServerPath'] . "' tareget='_parent'>" 
		. "[all done, continue to front page >> ]</a><br/>"
		. "<script language='javascript'>parent.showNextButton(); </script>";
	
	echo "</small></body></html>\n";
}

//=================================================================================================
//	UTILITY FUNCTIONS FOR THIS INSTALL SCRIPT
//=================================================================================================

//-------------------------------------------------------------------------------------------------
//	discover settings if already installed
//-------------------------------------------------------------------------------------------------

function getInstallState() {
	global $installState;

	$installState['adminUser'] = false;
	$installState['publicUser'] = false;
	$installState['school'] = false;
	$installState['static'] = false;
	$installState['wiki'] = false;

	//---------------------------------------------------------------------------------------------
	//	check for setup.inc.php in current location
	//---------------------------------------------------------------------------------------------

	if (file_exists($_SESSION['iInstallPath'] . 'setup.inc.php') == true) {
		include $_SESSION['iInstallPath'] . 'setup.inc.php';

		if ('' == $_SESSION['iServerPath']) { $_SESSION['iServerPath'] = $serverPath; }
		if ('' == $_SESSION['iDbName']) { $_SESSION['iDbName'] = $dbName; }
		if ('' == $_SESSION['iDbHost']) { $_SESSION['iDbHost'] = $dbHost; }
		if ('' == $_SESSION['iDbUser']) { $_SESSION['iDbUser'] = $dbUser; }
		if ('' == $_SESSION['iDbPass']) { $_SESSION['iDbPass'] = $dbPass; }
		if ('' == $_SESSION['iProxyEnabled']) { $_SESSION['iProxyEnabled'] = $proxyEnabled; }
		if ('' == $_SESSION['iProxyAddress']) { $_SESSION['iProxyAddress'] = $proxyAddress; }
		if ('' == $_SESSION['iProxyPort']) { $_SESSION['iProxyPort'] = $proxyPort; }
		if ('' == $_SESSION['iProxyUser']) { $_SESSION['iProxyUser'] = $proxyUser; }
		if ('' == $_SESSION['iProxyPass']) { $_SESSION['iProxyPass'] = $proxyPass; }
		//echo "found setup.inc.php<br/>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	check if we have a working connection to the database
	//---------------------------------------------------------------------------------------------	
	$report = dbTest();
	$pass = false;
	if (strpos($report, 'ajaxmsg') > 0) { $pass = true; }
	if (true == $pass) {

		//-----------------------------------------------------------------------------------------
		//	check for existing admin user
		//-----------------------------------------------------------------------------------------
		if (i$db->tableExists('users') == true) {
			$result = i$db->query("select * from users where role='admin'");
			if (mysql_num_rows($result) > 0) { $installState['adminUser'] = true; }
			//echo "admin user exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for existing admin user
		//-----------------------------------------------------------------------------------------
		if (i$db->tableExists('users') == true) {
			$result = i$db->query("select * from users where role='public'");
			if (mysql_num_rows($result) > 0) { $installState['publicUser'] = true; }
			//echo "public user exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for schools
		//-----------------------------------------------------------------------------------------
		if (i$db->tableExists('schools') == true) {
			$result = i$db->query("select UID from schools");
			if (mysql_num_rows($result) > 0) { $installState['school'] = true; }
			//echo "school exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for static pages
		//-----------------------------------------------------------------------------------------
		if (i$db->tableExists('static') == true) {
			$result = i$db->query("select UID from static");
			if (mysql_num_rows($result) > 0) { $installState['static'] = true; }
			//echo "front page exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for wiki front page
		//-----------------------------------------------------------------------------------------
		if (i$db->tableExists('wiki') == true) {
			$result = i$db->query("select UID from wiki");
			if (mysql_num_rows($result) > 0) { $installState['wiki'] = true; }
			//echo "wiki page exists<br/>\n";
		}	

	}		

}

//-------------------------------------------------------------------------------------------------
//	make the setup.inc.php config file
//-------------------------------------------------------------------------------------------------

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

//  (9) HTTP Proxy
//  awareNet may have to operate through a proxy

	\$proxyEnabled = '" . $_SESSION['iProxyEnabled'] . "';
	\$proxyAddress = '" . $_SESSION['iProxyAddress'] . "';
	\$proxyPort = '" . $_SESSION['iProxyPort'] . "';
	\$proxyUser = '" . $_SESSION['iProxyUser'] . "';
	\$proxyPass = '" . $_SESSION['iProxyPass'] . "';
    
?" . ">";

	$fH = fopen($fileName, 'w+');
	if ($fH == false) { return false; }
	fwrite($fH, $txt);
	fclose($fH);
	return true;
}

//==================================================================================================
//	apache modules
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// 	determines if a given module is installed on the system
//--------------------------------------------------------------------------------------------------

function apacheModuleInstalled($modName) {
	$amods = apache_get_modules();
	foreach($amods as $amod) { 
		//echo "module: $amod <br/>\n";
		if ($amod == $modName) { return true; } 
	}
	return false;
}

//==================================================================================================
//	filesystem
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// 	determines if a file/dir exists and is readable + writable
//--------------------------------------------------------------------------------------------------

function iis_extantrw($fileName) {
	if (file_exists($fileName)) {
		if (is_readable($fileName) == false) { return false; }
		if (is_writable($fileName) == false) { return false; }
	} else { return false; }
	return true;
}

function fileWriteHTA() {
	if (iis_extantrw($_SESSION['iInstallPath']) == false) { return false; }
	$fileName = $_SESSION['iInstallPath'] . '.htaccess';
	$fh = @fopen($fileName, 'w+');
	if (false == $fh) { return false; }
	fwrite($fh, embedHtAcess());
	fclose($fh);
	return true;
}

//==================================================================================================
//	database functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// make default tables and records
//--------------------------------------------------------------------------------------------------

function makeDefaultTables() {
	global $installState;

	//----------------------------------------------------------------------------------------------
	//	changes (revision) table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'changes';
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
	//	delitems table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['model'] = 'delitems';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'refTable' => 'VARCHAR(50)',
		'refUID' => 'VARCHAR(255)',
		'timestamp' => 'VARCHAR(20)' );

	$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10', 'refTable' => '6');
	$dbSchema['nodiff'] = array('UID', 'table', 'refUID', 'timestamp');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	downloads table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['model'] = 'downloads';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'filename' => 'VARCHAR(255)',
		'hash' => 'VARCHAR(255)',	
		'status' => 'VARCHAR(20)',	
		'timestamp' => 'VARCHAR(20)'
	);

	$dbSchema['indices'] = array('UID' => '10', 'filename' => '10');
	$dbSchema['nodiff'] = array('UID', 'filename', 'hash', 'timestamp');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	pagechannels table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['model'] = 'pagechannels';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'channelID' => 'VARCHAR(130)',
		'clients' => 'TEXT' );

	$dbSchema['indices'] = array('UID' => '10', 'channelID' => '8');
	$dbSchema['nodiff'] = array('UID', 'channelID', 'clients');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	pageclients table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['model'] = 'pageclients';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'channels' => 'TEXT',
		'inbox' => 'TEXT',
		'timestamp' => 'VARCHAR(20)' );

	$dbSchema['indices'] = array('UID' => '10');
	$dbSchema['nodiff'] = array('UID', 'channels', 'inbox', 'timestamp');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	migrated (static URL redirects)
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'migrated';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'fromUrl' => 'VARCHAR(255)',
		'toUrl' => 'VARCHAR(255)',
		'hitCount' => 'BIGINT(20)' );

	$dbSchema['indices'] = array('UID' => '10', 'fromUrl' => '30');
	$dbSchema['nodiff'] = array('UID', 'fromURL', 'toURL', 'hitCount');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	recordalias table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'recordalias';

	//----------------------------------------------------------------------------------------------
	//	recordalias table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'recordalias';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'refTable' => 'VARCHAR(100)',
		'refUID' => 'VARCHAR(30)',
		'aliaslc' => 'VARCHAR(255)',
		'alias' => 'VARCHAR(255)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

	$dbSchema['indices'] = array('UID' => '10', 'refTable' => '20', 'refUID' => '10', 'aliaslc' => '30');
	// no need to record changes to this table
	$dbSchema['nodiff'] = array('UID', 'refTable', 'refUID', 'aliaslc', 'alias');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => idbCreateUID(),		
		'refTable' => 'schools',
		'refUID' => $_SESSION['iSchoolUID'],	
		'aliaslc' => 'first-school',
		'alias' => 'First-School' );

	if (false == $installState['school']) {	i$db->save($data, $dbSchema); }

	$data = array(
		'UID' => idbCreateUID(),		
		'refTable' => 'users',
		'refUID' => $_SESSION['iAdminUID'],	
		'aliaslc' => 'admin',
		'alias' => 'Admin' );

	if (false == $installState['adminUser']) {	i$db->save($data, $dbSchema); }

	$data = array(
		'UID' => idbCreateUID(),		
		'refTable' => 'users',
		'refUID' => 'public',	
		'aliaslc' => 'public',
		'alias' => 'Public' );

	if (false == $installState['publicUser']) {	i$db->save($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	schools
	//----------------------------------------------------------------------------------------------	
	$schoolUID = idbCreateUID();

	//----------------------------------------------------------------------------------------------
	//	schools table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'schools';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'name' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'geocode' => 'TEXT',
		'country' => 'VARCHAR(255)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'alias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'alias' => '20');
	$dbSchema['nodiff'] = array('UID', 'alias');
	idbCreateTable($dbSchema);

	// create default school if none exist
	$data = array(
		'UID' => $_SESSION['iSchoolUID'],		
		'name' => 'First School',
		'description' => 'Describe your school here...',
		'geocode' => '',
		'country' => 'ZA',
		'editedOn' => i$db->datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'alias' => 'First-School' );
	
	if (false == $installState['school']) {	i$db->save($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	users table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['model'] = 'users';
	$dbSchema = array();
	$dbSchema['model'] = 'users';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'role' => 'VARCHAR(10)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'alias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'alias' => '20');
	$dbSchema['nodiff'] = array('UID', 'lastOnline', 'alias', 'password');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => $_SESSION['iAdminUID'],		
		'role' => 'admin',
		'school' => $_SESSION['iSchoolUID'],
		'grade' => 'Std. 12',
		'firstname' => 'System',	
		'surname' => 'Administrator',
		'username' => $_SESSION['iAUser'],	
		'password' => sha1($_SESSION['iAPass1'] . $_SESSION['iAdminUID']),
		'lang' => 'en',	
		'profile' => '',
		'permissions' => '',	
		'lastOnline' => i$db->datetime(),
		'createdOn' => i$db->datetime(),	
		'createdBy' => $_SESSION['iAdminUID'],
		'editedOn' => i$db->datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'alias' => 'Admin' );

	if (false == $installState['adminUser']) {	i$db->save($data, $dbSchema); }

	$data = array(
		'UID' => 'public',		
		'role' => 'public',
		'school' => $schoolUID,
		'grade' => 'Std. 1',
		'firstname' => 'Guest',	
		'surn