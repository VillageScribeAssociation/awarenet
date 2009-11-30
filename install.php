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

	$tables = idbListTables();
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
		if (idbTableExists('users') == true) {
			$result = idbQuery("select * from users where ofGroup='admin'");
			if (mysql_num_rows($result) > 0) { $installState['adminUser'] = true; }
			//echo "admin user exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for existing admin user
		//-----------------------------------------------------------------------------------------
		if (idbTableExists('users') == true) {
			$result = idbQuery("select * from users where ofGroup='public'");
			if (mysql_num_rows($result) > 0) { $installState['publicUser'] = true; }
			//echo "public user exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for schools
		//-----------------------------------------------------------------------------------------
		if (idbTableExists('schools') == true) {
			$result = idbQuery("select UID from schools");
			if (mysql_num_rows($result) > 0) { $installState['school'] = true; }
			//echo "school exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for static pages
		//-----------------------------------------------------------------------------------------
		if (idbTableExists('static') == true) {
			$result = idbQuery("select UID from static");
			if (mysql_num_rows($result) > 0) { $installState['static'] = true; }
			//echo "front page exists<br/>\n";
		}	

		//-----------------------------------------------------------------------------------------
		//	check for wiki front page
		//-----------------------------------------------------------------------------------------
		if (idbTableExists('wiki') == true) {
			$result = idbQuery("select UID from wiki");
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
	//	delitems table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['table'] = 'delitems';
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
	$dbSchema['table'] = 'downloads';
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
	$dbSchema['table'] = 'pagechannels';
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
	$dbSchema['table'] = 'pageclients';
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
	$dbSchema['table'] = 'migrated';
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
	$dbSchema['table'] = 'recordalias';

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

	if (false == $installState['school']) {	idbSave($data, $dbSchema); }

	$data = array(
		'UID' => idbCreateUID(),		
		'refTable' => 'users',
		'refUID' => $_SESSION['iAdminUID'],	
		'aliaslc' => 'admin',
		'alias' => 'Admin' );

	if (false == $installState['adminUser']) {	idbSave($data, $dbSchema); }

	$data = array(
		'UID' => idbCreateUID(),		
		'refTable' => 'users',
		'refUID' => 'public',	
		'aliaslc' => 'public',
		'alias' => 'Public' );

	if (false == $installState['publicUser']) {	idbSave($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	schools
	//----------------------------------------------------------------------------------------------	
	$schoolUID = idbCreateUID();

	//----------------------------------------------------------------------------------------------
	//	schools table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'schools';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'name' => 'VARCHAR(255)',
		'description' => 'TEXT',
		'geocode' => 'TEXT',
		'country' => 'VARCHAR(255)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

	// create default school if none exist
	$data = array(
		'UID' => $_SESSION['iSchoolUID'],		
		'name' => 'First School',
		'description' => 'Describe your school here...',
		'geocode' => '',
		'country' => 'ZA',
		'editedOn' => imysql_datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'recordAlias' => 'First-School' );
	
	if (false == $installState['school']) {	idbSave($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	users table
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'users';
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'lastOnline', 'recordAlias', 'password');
	idbCreateTable($dbSchema);

	$data = array(
		'UID' => $_SESSION['iAdminUID'],		
		'ofGroup' => 'admin',
		'school' => $_SESSION['iSchoolUID'],
		'grade' => 'Std. 12',
		'firstname' => 'System',	
		'surname' => 'Administrator',
		'username' => $_SESSION['iAUser'],	
		'password' => sha1($_SESSION['iAPass1'] . $_SESSION['iAdminUID']),
		'lang' => 'en',	
		'profile' => '',
		'permissions' => '',	
		'lastOnline' => imysql_datetime(),
		'createdOn' => imysql_datetime(),	
		'createdBy' => $_SESSION['iAdminUID'],
		'editedOn' => imysql_datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'recordAlias' => 'Admin' );

	if (false == $installState['adminUser']) {	idbSave($data, $dbSchema); }

	$data = array(
		'UID' => 'public',		
		'ofGroup' => 'public',
		'school' => $schoolUID,
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
		'createdBy' => $_SESSION['iAdminUID'],
		'editedOn' => imysql_datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'recordAlias' => 'Public' );

	if (false == $installState['publicUser']) {	idbSave($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	school announcements
	//----------------------------------------------------------------------------------------------
	$dbSchema = array();
	$dbSchema['table'] = 'announcements';
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'createdOn' => 'DATETIME',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'joined' => 'DATETIME',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'createdOn' => 'DATETIME',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

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
		'hitcount' => 'BIGINT(20)',
		'createdOn' => 'DATETIME',
		'createdBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'notices' => 'TEXT',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

	$dbSchema['indices'] = array('UID' => '10', 'user' => '20');
	$dbSchema['nodiff'] = array('UID', 'notices');
	idbCreateTable($dbSchema);


	//----------------------------------------------------------------------------------------------
	//	projectrevisions table
	//----------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['table'] = 'projectrevisions';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'refUID' => 'VARCHAR(30)',
		'content' => 'TEXT',
		'type' => 'VARCHAR(50)',
		'reason' => 'VARCHAR(255)',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME' );

	$dbSchema['indices'] = array('UID' => '10', 'refUID' => '20');
	$dbSchema['nodiff'] = array('UID','refUID','content','type','reason','editedBy','editedOn');
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
		'joined' => 'DATETIME',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

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
		'content' => 'TEXT',
		'talk' => 'TEXT',
		'locked' => 'VARCHAR(20)',
		'createdBy' => 'VARCHAR(30)',
		'createdOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
		'editedOn' => 'DATETIME',
		'hitcount' => 'BIGINT(20)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
	$dbSchema['nodiff'] = array('UID', 'recordAlias');
	idbCreateTable($dbSchema);

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
		'active' => 'VARCHAR(10)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)' );

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
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)',
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
		'createdBy' => $_SESSION['iAdminUID'],
		'editedOn' => imysql_datetime(),
		'editedBy' => $_SESSION['iAdminUID'],
		'recordAlias' => 'Front-Page' );
	if (false == $installState['static']) {	idbSave($data, $dbSchema); }

	//----------------------------------------------------------------------------------------------
	//	sync
	//----------------------------------------------------------------------------------------------	
	$dbSchema = array();
	$dbSchema['table'] = 'sync';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'source' => 'VARCHAR(30)',
		'type' => 'VARCHAR(50)',
		'data' => 'TEXT',	
		'peer' => 'VARCHAR(30)',
		'status' => 'VARCHAR(30)',
		'received' => 'VARCHAR(30)',
		'timestamp' => 'VARCHAR(20)'
	);

	$dbSchema['indices'] = array('UID' => '10');
	$dbSchema['nodiff'] = array('UID', 'source', 'type', 'data', 'peer', 'status', 'received', 'timestamp');
	idbCreateTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	userlogins
	//----------------------------------------------------------------------------------------------	

	$dbSchema = array();
	$dbSchema['table'] = 'userlogin';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',
		'userUID' => 'VARCHAR(255)',
		'serverurl' => 'VARCHAR(255)',
		'logintime' => 'DATETIME',
		'lastseen' => 'VARCHAR(20)',				
		'editedOn' => 'DATETIME',	
		'editedBy' => 'VARCHAR(30)'
	);


	$dbSchema['indices'] = array('UID' => '10', 'userUID' => '10');

	$dbSchema['nodiff'] = array(	'UID', 'userUID', 'serverurl', 'logintime', 
									'lastseen', 'editedOn', 'editedBy'	);

	idbCreateTable($dbSchema);

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
		'hitcount' => 'BIGINT(20)',
		'recordAlias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');

	$dbSchema['nodiff'] = array( 'UID', 'title', 'content', 'talk', 'locked', 'createdBy', 
								 'createdOn', 'editedBy', 'editedOn', 'hitcount', 
								 'recordAlias' );

	idbCreateTable($dbSchema);

	/*
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
	*/

	//---------------------------------------------------------------------------------------------
	//	wikirevisions
	//---------------------------------------------------------------------------------------------	
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

//-------------------------------------------------------------------------------------------------
// add the admin user, the public user and the admin users school
//-------------------------------------------------------------------------------------------------

function makeDefaultRecords() {
	//---------------------------------------------------------------------------------------------
	//	admin user
	//---------------------------------------------------------------------------------------------
	
}

//-------------------------------------------------------------------------------------------------
// create a random ID
//-------------------------------------------------------------------------------------------------

function idbCreateUID() {
	$tempUID = "";
	for ($i = 0; $i < 16; $i++) { $tempUID .= "" . imk_rand(); }
	return substr($tempUID, 0, 18);
}

function imk_rand() { srand(imake_seed()); return rand(); }

function imake_seed() {
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}

//-------------------------------------------------------------------------------------------------
// make a table + indices
//-------------------------------------------------------------------------------------------------

function idbCreateTable($dbSchema) {
	$report = '';

	//---------------------------------------------------------------------------------------------
	//	check if table already exists
	//---------------------------------------------------------------------------------------------
	if (idbTableExists($dbSchema['table']) == true) {
		$report .= "[*] Table " . $dbSchema['table'] . " already exists.<br/>\n";
		return $report;
	}

	$report .= "[>] Creating database table " . $dbSchema['table'] . "...<br/>\n";

	//---------------------------------------------------------------------------------------------
	//	create table
	//---------------------------------------------------------------------------------------------
	$sql = "create table " . $dbSchema['table'] . " (\n";
	$fields = array();
	foreach($dbSchema['fields'] as $fieldName => $fieldType) {
		$fields[] = '  ' . $fieldName . ' ' . $fieldType;
	}
	$sql .= implode(",\n", $fields) . ");\n";

	idbQuery($sql);

	//---------------------------------------------------------------------------------------------
	//	indices
	//---------------------------------------------------------------------------------------------
	foreach($dbSchema['indices'] as $idxField => $idxSize) {
		$idxName = 'idx' . $dbSchema['table'] . $idxField;
		if ($idxSize == '') {
			$sql = "create index $idxName on " . $dbSchema['table'] . ";";
		} else {
			$sql = "create index $idxName on " . $dbSchema['table'] . " (" . $idxField . "(10));";
		}
		idbQuery($sql);
	}

	return $report;
}

//--------------------------------------------------------------------------------------------------
// 	check if a table exists in the database
//--------------------------------------------------------------------------------------------------

function idbTableExists($tableName) {
	$sql = "SHOW TABLES FROM " . $_SESSION['iDbName'];
	$result = idbQuery($sql);
	while ($row = mysql_fetch_assoc($result)) {
	  foreach ($row as $key => $someTable) {
		if ($someTable == $tableName) { return true; }
	  }
	}
	return false;
}

//--------------------------------------------------------------------------------------------------
// 	make a list of all tables in database
//--------------------------------------------------------------------------------------------------

function idbListTables() {
	$tables = array();
	$result = idbQuery("show tables from " . $_SESSION['iDbName']);
	while ($row = mysql_fetch_assoc($result)) { foreach ($row as $table) { $tables[] = $table; } }
	return $tables;
}

//-------------------------------------------------------------------------------------------------
// execute a query, return handle
//-------------------------------------------------------------------------------------------------

function idbQuery($query) {
	// connect to database
	$connect = @mysql_pconnect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass'])
			   or die("no connect");

	mysql_select_db($_SESSION['iDbName'], $connect); 

	$result = mysql_query($query, $connect) ;
			  //or die("<h1>Database Error... sorry :-(</h1>" . mysql_error() ."<p>" . $query);

	return $result;
}

//-------------------------------------------------------------------------------------------------
// save a record given a dbSchema array and an array of field values, returns false on failue
//-------------------------------------------------------------------------------------------------

function idbSave($data, $dbSchema) {
	if (array_key_exists('UID', $data) == false) { return false; }	
	if (strlen(trim($data['UID'])) < 4) { return false; }

	//---------------------------------------------------------------------------------------------
	//	discover if the record already exists, take no action if it does
	//---------------------------------------------------------------------------------------------
	if (idbRecordExists($dbSchema['table'], $data['UID']) == true) { 

		$_SESSION['sMessage'] .= '[*] Record ' . $data['UID'] . ' already exists in table '
							  . $dbSchema['table'] . ", leaving as is.<br/>\n";

		return false; 
	}

	//---------------------------------------------------------------------------------------------
	//	delete the current record, if it exists (removed, failsafe)
	//---------------------------------------------------------------------------------------------
	//$sql = "delete from " . $dbSchema['table'] . " where UID='" . $data['UID'] . "'";
	//idbQuery($sql);

	//---------------------------------------------------------------------------------------------
	//	save a new one
	//---------------------------------------------------------------------------------------------

	$sql = "insert into " . $dbSchema['table'] . " values (";
	foreach ($dbSchema['fields'] as $fName => $fType) {
	  if (strlen($fName) > 0) {
		$quote = true;
		$value = ''; // . $fName . ':';

		//-----------------------------------------------------------------------------------------
		//	some field types should be quotes, some not
		//-----------------------------------------------------------------------------------------
		switch (strtolower($fType)) {
			case 'bigint': 		$quote = false; break;
			case 'tinyint';		$quote = false; break;
		}

		//-----------------------------------------------------------------------------------------
		//	clean the value and add to array
		//-----------------------------------------------------------------------------------------
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
// 	get table schema in Kapenta's dbSchema format (jagged array)
//--------------------------------------------------------------------------------------------------
//	note that nodiff is not generated, as this is not known by the DBMS

function idbGetSchema($tableName) {
	if (idbTableExists($tableName) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	create dbSchema array
	//----------------------------------------------------------------------------------------------
	$dbSchema = array(	'table' => $tableName, 'fields' => array(), 
						'indices' => array(), 'nodiff' => array()	);

	//----------------------------------------------------------------------------------------------
	//	add fields
	//----------------------------------------------------------------------------------------------
	$sql = "describe " . isqlMarkup($tableName);
	$result = idbQuery($sql);
	while ($row = mysql_fetch_assoc($result)) 
		{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

	//----------------------------------------------------------------------------------------------
	//	add indices
	//----------------------------------------------------------------------------------------------
	$sql = "show indexes from " . isqlMarkup($tableName);
	$result = idbQuery($sql);
	while ($row = mysql_fetch_assoc($result)) 
		{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

	return $dbSchema;
}

//--------------------------------------------------------------------------------------------------
// 	return a dbSchema array as html
//--------------------------------------------------------------------------------------------------

function idbSchemaToHtml($dbSchema) {
	$html = "<h2>" . $dbSchema['table'] . " (dbSchema)</h2>\n";
	$rows = array(array('Field', 'Type', 'Index'));
	foreach($dbSchema['fields'] as $field => $type) {
		$idx = '';
		if (array_key_exists($field, $dbSchema['indices'])) { $idx = $dbSchema['indices'][$field]; }
		$rows[] = array($field, $type, $idx);
	}

	$html .= iarrayToHtmlTable($rows, true, true);
	return $html;
}

//--------------------------------------------------------------------------------------------------
// 	render a 2d array as a table
//--------------------------------------------------------------------------------------------------

function iarrayToHtmlTable($ary, $wireframe = false, $firstrowtitle = false) {
	if (false == $wireframe) { 
		$html = "<table noborder width='100%'>";
		foreach($ary as $row) {
			$html .= "\t<tr>\n";
			foreach($row as $col) {	$html .= "\t\t<td>" . $col . "</td>\n"; }	
			$html .= "\t</tr>\n"; 
		}
		$html .= "</table>";

	} else {
		$html = "<table class='wireframe' width='100%'>";
		foreach($ary as $row) {
			$tdClass = 'wireframe';
			if (true == $firstrowtitle) { $firstrowtitle = false; $tdClass = 'title'; }
			$html .= "\t<tr>\n";
			foreach($row as $col) {	$html .= "\t\t<td class='". $tdClass ."'>". $col ."</td>\n"; }	
			$html .= "\t</tr>\n"; 
		}
		$html .= "</table>";
	}

	return $html;
}

//-------------------------------------------------------------------------------------------------
// 	get/convert the current date/time into mySQL format
//-------------------------------------------------------------------------------------------------

function imysql_dateTime() { return gmdate("Y-m-j H:i:s", time()); }
function imk_mysql_dateTime($date) { return gmdate("Y-m-j H:i:s", $date); }

//-------------------------------------------------------------------------------------------------
// 	sanitize a value before using it in a sql statement, to prevent SQL injection, some XSS, etc
//-------------------------------------------------------------------------------------------------

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

//-------------------------------------------------------------------------------------------------
// 	remove sql markup
//-------------------------------------------------------------------------------------------------

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

	//---------------------------------------------------------------------------------------------
	// legacy markup, from kapenta 1, remove these if not migrating old data
	//---------------------------------------------------------------------------------------------

	$text = str_replace("[`|squote]", "'", $text);
	$text = str_replace("[`|quote]", "\"", $text);
	$text = str_replace("[`|semicolon]", ";", $text);

	return $text;
}

//-------------------------------------------------------------------------------------------------
// 	remove sql markup from an array (no nested arrays)
//-------------------------------------------------------------------------------------------------

function isqlRMArray($ary) {
	$retVal = array();
	foreach ($ary as $key => $val) {
		$retVal[$key] = isqlRemoveMarkup($val);
	}
	return $retVal;
}

//-------------------------------------------------------------------------------------------------
// 	check if a record with given UID exists in a table
//-------------------------------------------------------------------------------------------------

function idbRecordExists($table, $UID) {
	$sql = "SELECT * FROM $table WHERE UID='" . isqlMarkup($UID) . "'";
	$result = idbQuery($sql);
	if (mysql_num_rows($result) == 0) { return false; }
	return true;
}

//-------------------------------------------------------------------------------------------------
// 	test database settings
//-------------------------------------------------------------------------------------------------

function dbTest() {
	$report = '';
	$pass = true;

	if (trim($_SESSION['iDbName']) == '') 
		{ $report .= "[*] Database name must not be blank.<br/>\n"; }

	//---------------------------------------------------------------------------------------------
	//	try connect to server
	//---------------------------------------------------------------------------------------------

	$connect = @mysql_pconnect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass']);
	//$connect = mysql_connect($_SESSION['iDbHost'], $_SESSION['iDbUser'], $_SESSION['iDbPass']);

	if ($connect == false) {
		$pass = false;
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

	//---------------------------------------------------------------------------------------------
	//	try access database
	//---------------------------------------------------------------------------------------------
	if (true == $pass) {
		$db = @mysql_select_db($_SESSION['iDbName'], $connect); 	
		if ($db == false) {
			$pass = false;
			$report .= "[*] Could not connect to database.<br/>";
			$report .= "[>] dbName: " . $_SESSION['iDbName'] . "<br/>";
			$report .= "[i] Please confirm database exists.<br/>";
		} else {
			$report .= "[|] granted access to database ... OK<br/>\n";
		}
	}

	//---------------------------------------------------------------------------------------------
	//	try create and delete a table
	//---------------------------------------------------------------------------------------------

	if (true == $pass) {
		$dbSchema = array();
		$dbSchema['table'] = 'canary';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'test' => 'VARCHAR(10)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'	);
	
		$dbSchema['indices'] = array('UID' => '10');
		$dbSchema['nodiff'] = array('UID');
		idbCreateTable($dbSchema);
	
		if (idbTableExists('canary') == false) {
			$pass = false;
			$report .= "[*] could not create test table 'canary'<br/>\n";
		} else {
			$report .= "[|] created table 'canary' ... OK<br/>\n";
		}
	
		idbQuery("drop table canary");

		if (idbTableExists('canary') == true) {
			$pass = false;
			$report .= "[*] could not delete test table<br/>\n";
		} else {
			$report .= "[|] deleted table 'canary' ... OK<br/>\n";
		}

	}

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------

	if ($pass == false) { $report .= "<p><span class='ajaxerror'>fail</span></p>"; }
	else { $report .= "<p><span class='ajaxmsg'>pass</span></p>"; }

	return $report;
}

//=================================================================================================
//	code repository
//=================================================================================================

//-------------------------------------------------------------------------------------------------
//	use CURL to dowload 
//-------------------------------------------------------------------------------------------------

function curlHttpGet($url) {
	if (function_exists('curl_init') == false) { return false; }

	$ch = curl_init($url);

	//---------------------------------------------------------------------------------------------
	//	we may have to use a proxy
	//---------------------------------------------------------------------------------------------
	if ($_SESSION['iProxyEnabled'] == 'yes') {
		$credentials = $_SESSION['iProxyUser'] . ':' . $_SESSION['iProxyPass'];
		curl_setopt($ch, CURLOPT_PROXY, $_SESSION['iProxyAddress']);
		curl_setopt($ch, CURLOPT_PROXYPORT, $_SESSION['iProxyPort']);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		if (trim($credentials) != ':') {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
		}
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

//-------------------------------------------------------------------------------------------------
//	download repository list and convert into an array
//-------------------------------------------------------------------------------------------------

function getRepositoryList($repository) {
	echo "[>] Downlading list of files...<br/>\n";
	$rList = array();
	$raw = '';
	if (function_exists('curl_init') == true) { $raw = curlHttpGet($repository); } 
	else { $raw = implode(file($repository)); }

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
				$content = '';
				$contentUrl = $repositoryDoor . $item['UID'];

				if (function_exists('curl_init') == true) { $content = curlHttpGet($contentUrl); }
				else {$content = @implode(@file($contentUrl)); }

				if ($content == false) {
					echo "[*] Error: could not download $outFile (UID:" . $item['UID'] . ")<br/>\n";	
					$retryList[$item['UID']] = $item;	// failed, retry

				} else {
					//------------------------------------------------------------------------------
					//	content is base64 encoded
					//------------------------------------------------------------------------------
					$content = base64_decode($content);

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

//=================================================================================================
//	EMBEDDED ASSETS (css, js, etc)
//=================================================================================================

function embedInstallCss() {
	$css = "QGNoYXJzZXQgIlVURi04IjsKCi8qIG1haW4gYm9keSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi8K"
         . "CmJvZHkgewoJYmFja2dyb3VuZDogI2FhYWFhYTsKCWZvbnQtZmFtaWx5OiBWZXJkYW5hLCBBcmlhbCwg"
         . "SGVsdmV0aWNhLCBzYW5zLXNlcmlmOwoJZm9udC1zaXplOiBzbWFsbGVyOwoJY29sb3I6ICMzMDMwMzA7"
         . "CglwYWRkaW5nOiAwcHg7CgltYXJnaW46IDBweDsKfQoKYSB7IHRleHQtZGVjb3JhdGlvbjogbm9uZTsg"
         . "Y29sb3I6ICMzNjVhMTA7IH0KYTpob3ZlciB7IHRleHQtZGVjb3JhdGlvbjogdW5kZXJsaW5lOyBjb2xv"
         . "cjogIzM2NWExMDsgfQoKYSBoMSB7Cgljb2xvcjogIzMwMzAzMDsKfQoKYSBoMiB7Cgljb2xvcjogIzMw"
         . "MzAzMDsKfQoKdGFibGUgeyAKCWJhY2tncm91bmQ6ICNmZmZmZmY7Cn0KCmhyIHsKCWJvcmRlci10b3A6"
         . "IDFweCBkYXNoZWQgIzUyN2QyNjsKCWNvbG9yOiAjZmZmOwoJYmFja2dyb3VuZC1jb2xvcjogI2ZmZjsK"
         . "CWhlaWdodDogMXB4Owp9CgovKiB0b3AgbWVudSAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovCgpp"
         . "bWcubWVudTEgewoJcGFkZGluZy1sZWZ0OiAwcHg7CglwYWRkaW5nLXRvcDogMHB4OwoJcGFkZGluZy1y"
         . "aWdodDogMHB4OwoJcGFkZGluZy1ib3R0b206IDBweDsKfQoKYS5tZW51IHsKCWNvbG9yOiAjZmZmZmZm"
         . "OwkKCWJhY2tncm91bmQtY29sb3I6ICMwMDAwMDA7Cn0KCi8qIGltYWdlcyAtLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0gKi8KCmRpdi5jYXB0aW9uIHsKCWZsb2F0OiBsZWZ0OwoJYmFja2dyb3VuZC1jb2xv"
         . "cjogI2VlZWVlZTsKfQoKZGl2LmNhcHRpb25wYWQgewoJZmxvYXQ6IGxlZnQ7CgliYWNrZ3JvdW5kLWNv"
         . "bG9yOiAjZWVlZWVlOwoJbWFyZ2luLXJpZ2h0OiAxMHB4Owp9CgovKiBzdWJtZW51IGl0ZW1zIC0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tICovCgpzcGFuLnN1Ym1lbnUgeyAKCWZvbnQtc2l6ZTogOTAlOyAKCXBhZGRp"
         . "bmctbGVmdDogMThweDsKCWNvbG9yOiAjZmZmZmZmOwoJZm9udC1zaXplOiAxOXB4OwoJbWFyZ2luOiAw"
         . "cHg7CglwYWRkaW5nOiAwcHg7CglwYWRkaW5nLWxlZnQ6IDEwcHg7CglwYWRkaW5nLXJpZ2h0OiAxMHB4"
         . "OwoJYmFja2dyb3VuZC1jb2xvcjogI2FhYWFhYTsKCXZlcnRpY2FsLWFsaWduOiB0b3A7Cn0KCnNwYW4u"
         . "c3VibWVudTpob3ZlciB7IAoJYmFja2dyb3VuZC1jb2xvcjogI2JiYmJiYjsKfQoKc3Bhbi5zdWJtZW51"
         . "IGEgewoJY29sb3I6ICNmZmZmZmY7Cgl0ZXh0LWRlY29yYXRpb246IG5vbmU7Cn0KCi8qIG5hdmlnYXRp"
         . "b24gYm94ZXMgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gKi8KCmRpdi5uYXZib3ggeyAKCWZvbnQtc2l6ZTogOTAl"
         . "OyAKCXBhZGRpbmctbGVmdDogMjBweDsKCWNvbG9yOiAjZmZmZmZmOwoJZm9udC1zaXplOiAyMHB4OwoJ"
         . "cGFkZGluZzogMnB4OwoJcGFkZGluZy1sZWZ0OiAxMnB4OwoJYmFja2dyb3VuZC1jb2xvcjogIzRlNGU0"
         . "ZTsKCS1tb3otdXNlci1zZWxlY3Q6IG5vbmU7Cn0KCmRpdi5uYXZib3ggYSB7Cgljb2xvcjogIzQ0NDQ0"
         . "NDsKfQoKaW1nLm5hdmJveGJ0biB7CglmbG9hdDogcmlnaHQ7IAoJcG9zaXRpb246IHJlbGF0aXZlOyAK"
         . "CXRvcDogLTIxcHg7IAoJbGVmdDogLTVweDsKfQoKLyogaW5kZW50ZWQgZGl2IC0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLSAqLwoKZGl2LmluZGVudCB7CglwYWRkaW5nOiAwcHggMHB4IDBweCAxNHB4Owp9CgovKiB3"
         . "aXJlZnJhbWUgdGFibGVzIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovCgp0YWJsZS53aXJlZnJhbWUgewoJYm9y"
         . "ZGVyLXdpZHRoOiAxcHggMXB4IDFweCAxcHg7Cglib3JkZXItc3BhY2luZzogMHB4OwoJcGFkZGluZzog"
         . "MHB4IDBweCAwcHggMHB4OwoJYm9yZGVyLXN0eWxlOiBzb2xpZCBzb2xpZCBzb2xpZCBzb2xpZDsKCWJv"
         . "cmRlci1jb2xvcjogI2RkZGRkZCAjZGRkZGRkICNkZGRkZGQgI2RkZGRkZDsKCS1tb3otYm9yZGVyLXJh"
         . "ZGl1czogMHB4IDBweCAwcHgKfQoKdGQud2lyZWZyYW1lIHsKCWJvcmRlci13aWR0aDogMXB4IDFweCAx"
         . "cHggMHB4OwoJcGFkZGluZzogMXB4IDFweCAxcHggMXB4OwoJYm9yZGVyLXN0eWxlOiBzb2xpZCBzb2xp"
         . "ZCBzb2xpZCBzb2xpZDsKCWJvcmRlci1jb2xvcjogI2RkZGRkZCAjZGRkZGRkICNkZGRkZGQgI2RkZGRk"
         . "ZDsKCS1tb3otYm9yZGVyLXJhZGl1czogMHB4IDBweCAwcHggMHB4Owp9Cgp0ZC50aXRsZSB7Cglib3Jk"
         . "ZXItd2lkdGg6IDFweCAxcHggMHB4IDBweDsKCXBhZGRpbmc6IDFweCAxcHggMXB4IDFweDsKCWJvcmRl"
         . "ci1zdHlsZTogc29saWQgc29saWQgc29saWQgc29saWQ7Cglib3JkZXItY29sb3I6ICNkZGRkZGQgI2Rk"
         . "ZGRkZCAjZGRkZGRkICNkZGRkZGQ7CgliYWNrZ3JvdW5kOiAjZGRkZGRkOwoJLW1vei1ib3JkZXItcmFk"
         . "aXVzOiAwcHggMHB4IDBweCAwcHg7Cn0KCi8qIGltYWdlcyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0gKi8KCmltZy5pbXRodW1iIHsKCWZsb2F0OiBsZWZ0Owp9CgppbWcuaW13aWR0aDMwMCB7CglmbG9h"
         . "dDogbGVmdDsKCXBhZGRpbmctcmlnaHQ6IDEwcHg7Cn0KCmltZy5pbXdpZHRoZWRpdG9yIHsKCQp9Cgov"
         . "KiBhamF4IG1lc3NhZ2VzIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovCgpzcGFuLmFqYXhtc2cgewoJYmFj"
         . "a2dyb3VuZC1jb2xvcjogZ3JlZW47CglwYWRkaW5nLXJpZ2h0OiAxMHB4OwoJcGFkZGluZy1sZWZ0OiAx"
         . "MHB4Owp9CgpzcGFuLmFqYXhlcnJvciB7CgliYWNrZ3JvdW5kLWNvbG9yOiByZWQ7CglwYWRkaW5nLXJp"
         . "Z2h0OiAxMHB4OwoJcGFkZGluZy1sZWZ0OiAxMHB4Owp9CgovKiBpbmxpbmUgcXVvdGUgYm94ZXMgLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tICovCgpkaXYuaW5saW5lcXVvdGUgewoJbWFyZ2luOiA0cHggNHB4IDRweCA0cHg7"
         . "CglkaXNwbGF5OiBibG9jazsKCWJvcmRlci10b3Atd2lkdGg6IDFweDsKCWJvcmRlci1yaWdodC13aWR0"
         . "aDogMXB4OwoJYm9yZGVyLWJvdHRvbS13aWR0aDogMXB4OwoJYm9yZGVyLWxlZnQtd2lkdGg6IDFweDsK"
         . "CWJvcmRlci10b3Atc3R5bGU6IHNvbGlkOwoJYm9yZGVyLXJpZ2h0LXN0eWxlOiBzb2xpZDsKCWJvcmRl"
         . "ci1ib3R0b20tc3R5bGU6IHNvbGlkOwoJYm9yZGVyLWxlZnQtc3R5bGU6IHNvbGlkOwoJYm9yZGVyLXRv"
         . "cC1jb2xvcjogI2RkZDsKCWJvcmRlci1yaWdodC1jb2xvcjogI2RkZDsKCWJvcmRlci1ib3R0b20tY29s"
         . "b3I6ICNkZGQ7Cglib3JkZXItbGVmdC1jb2xvcjogI2RkZDsKCWJhY2tncm91bmQtY29sb3I6ICNFRUVF"
         . "RUU7Cglmb250LWZhbWlseTogR2VuZXZhLCBBcmlhbCwgSGVsdmV0aWNhLCBzYW5zLXNlcmlmOwoJY29s"
         . "b3I6ICMzMzMzMzM7Cglmb250LXNpemU6IDEycHg7Cgl0ZXh0LWRlY29yYXRpb246IG5vbmU7Cglmb250"
         . "LXdlaWdodDogMjAwOwogfQoKLyogY2hhdCB3aW5kb3dzIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0t"
         . "LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLSAqLwoK"
         . "ZGl2IC5kcmFnIHsKCWJhY2tncm91bmQtaW1hZ2U6IHVybCgvdGhlbWVzL2Nsb2NrZmFjZS9pbWFnZXMv"
         . "Y2hhdC5wbmcpOwoJcG9zaXRpb246IHJlbGF0aXZlOwoJd2lkdGg6IDIwMHB4OwoJbWFyZ2luOiA0cHgg"
         . "NHB4IDRweCA0cHg7CglkaXNwbGF5OiBibG9jazsKCWJvcmRlci10b3Atd2lkdGg6IDFweDsKCWJvcmRl"
         . "ci1yaWdodC13aWR0aDogMXB4OwoJYm9yZGVyLWJvdHRvbS13aWR0aDogMXB4OwoJYm9yZGVyLWxlZnQt"
         . "d2lkdGg6IDFweDsKCWJvcmRlci10b3Atc3R5bGU6IHNvbGlkOwoJYm9yZGVyLXJpZ2h0LXN0eWxlOiBz"
         . "b2xpZDsKCWJvcmRlci1ib3R0b20tc3R5bGU6IHNvbGlkOwoJYm9yZGVyLWxlZnQtc3R5bGU6IHNvbGlk"
         . "OwoJYm9yZGVyLXRvcC1jb2xvcjogI2RkZDsKCWJvcmRlci1yaWdodC1jb2xvcjogI2RkZDsKCWJvcmRl"
         . "ci1ib3R0b20tY29sb3I6ICNkZGQ7Cglib3JkZXItbGVmdC1jb2xvcjogI2RkZDsKCWJhY2tncm91bmQt"
         . "Y29sb3I6ICNFRUVFRUU7Cglmb250LWZhbWlseTogR2VuZXZhLCBBcmlhbCwgSGVsdmV0aWNhLCBzYW5z"
         . "LXNlcmlmOwoJY29sb3I6ICMzMzMzMzM7Cglmb250LXNpemU6IDEycHg7Cgl0ZXh0LWRlY29yYXRpb246"
         . "IG5vbmU7Cglmb250LXdlaWdodDogMjAwOwogfQoKZGl2IC5jd3RleHQgewoJaGVpZ2h0OiAyNTBweDsK"
         . "CXdpZHRoOiAyMDBweDsKCW92ZXJmbG93OiBhdXRvOwoJdGV4dC1hbGlnbjogbGVmdDsKfQoKc3BhbiAu"
         . "bXljaGF0IHsKCWNvbG9yOiAjMzY1YTEwOwoJZm9udC1zaXplOiAxMnB4OwkKfQo=";

	return base64_decode($css);
}

function embedHtAcess() {
	$txt = "RewriteEngine on
        	RewriteBase /
        	RewriteCond %{REQUEST_FILENAME} !-f
        	RewriteCond %{REQUEST_FILENAME} !-d
	        RewriteRule ^(.*)$ index.php?q=$1 [L,QSA]
			";

	return $txt;
}

?>
