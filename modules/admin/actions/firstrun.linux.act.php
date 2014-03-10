<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');

//-------------------------------------------------------------------------------------------------
//*	configure awareNet from installer
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check that this is not already complete
	//---------------------------------------------------------------------------------------------
	if ('yes' == $kapenta->registry->get('firstrun.complete')) {
		$session->msg('awareNet initialized.');
		$page->do302('');
	}

	// override default max execution time in case Apache has not been restarted.
	ini_set('max_execution_time', 900);
	
	//---------------------------------------------------------------------------------------------
	//	set some registry defaults
	//---------------------------------------------------------------------------------------------		
	$dbrDefault = array(
		'firstrun.dbr.created' => 'no',
		'firstrun.dbr.granted' => 'no',
		'firstrun.dbr.installed' => 'no',
		'firstrun.dbr.name' => 'awarenet',
		'firstrun.dbr.host' => 'localhost',
		'firstrun.dbr.user' => 'root',
		'firstrun.dbr.password' => '',
		'firstrun.db.user' => 'awarenet',
		'firstrun.db.password' => $kapenta->createUID(),
		'firstrun.adminuid' => $kapenta->createUID()
	);
	
	foreach($dbrDefault as $key => $value) {
		if ('' == $kapenta->registry->get($key)) { $kapenta->registry->set($key, $value); }
	}
			
	//---------------------------------------------------------------------------------------------
	//	run as admin
	//---------------------------------------------------------------------------------------------
	$user->role = 'admin';
	echo $theme->expandBlocks("[[:theme::ifscrollheader:]]", '');	
	echo "<h1>Configuring awareNet</h1>";
	
	$dba = new KDBAdminDriver();
	
	//---------------------------------------------------------------------------------------------
	//	define some forms
	//---------------------------------------------------------------------------------------------

	$frmMySQLRoot = '' 
		. "Please enter your MySQL root user and password to create `awarenet` database:<br/>\n"
		. "<br/>"
		. "<form name='frmSetDbr' method='POST'>\n"
		. "<input type='hidden' name='action' value='set_dbr' />"
		. "<b>Username:</b> <input type='text' name='firstrun_dbr_user' "
		 . "value='" . $kapenta->registry->get('firstrun.dbr.user') . "'/>&nbsp; "
		. "<b>Password:</b> <input type='text' name='firstrun_dbr_password' "
		 . "value='" . $kapenta->registry->get('firstrun.dbr.password') . "'/>&nbsp; "
		. "<input type='submit' value='Retry &gt;&gt;' />"
		. "</form><br/>\n"
		. "<small>Default values for XAMPP are 'root' and no password.</small>";

	$frmMySQLDetail = ''
		. "<p>Alternatively, if you already have a database set up, please set the details:"
		. "<form name='frmSetMySQL' method='POST'>\n"
		. "<input type='hidden' name='action' value='set_mysql' />\n"
		. "<table noborder>\n"
		. "  <tr>\n"
		. "    <td><b>Database host:</b></td>\n"
		. "    <td><input type='text' name='kapenta_db_host' value='" . $kapenta->registry->get('firstrun.dbr.host') . "'> "
		 . "   <small>(use <i>localhost</i> if unsure)</small></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Database name:</b></td>\n"
		. "    <td><input type='text' name='kapenta_db_name' value='" . $kapenta->registry->get('firstrun.dbr.name') . "'> "
		 . "   <small>(eg: awarenet)</small></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Database user:</b></td>\n"
		. "    <td><input type='text' name='kapenta_db_user' value='" . $kapenta->registry->get('firstrun.dbr.user') . "'><small></small></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Database password:</b></td>\n"
		. "    <td><input type='text' name='kapenta_db_password' value='" . $kapenta->registry->get('firstrun.dbr.password') . "'></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b></b></td>\n"
		. "    <td><input type='submit' value='Create Database Settings'></td>\n"
		. "  </tr>\n"
		. "</table>\n"
		. "</form>\n";


	$frmRecoveryPass = ''
		. "<p>Please enter a recovery password.  This can be used to repair your awareNet "
		. "installation should it be damaged, or to regain control of an administrator account."
		. "</p>"
		. "<form name='setRecovery' method='POST'>\n"
		. "<input type='hidden' name='action' value='set_recovery'>\n"
		. "<table noborder>\n"
		. "  <tr>\n"
		. "    <td><b>Recovery Password:</b></td>\n"
		. "    <td><input type='password' name='recovery1' /></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Confirm:</b></td>\n"
		. "    <td><input type='password' name='recovery2' /></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b></b></td>\n"
		. "    <td><input type='submit' value='Set Recovery Password' /></td>\n"
		. "  </tr>\n"
		. "</table>"
		. "</form>\n"
		. "";

	$adminuser = $kapenta->registry->get('firstrun.adminuser');

	$frmAdminUser = ''
		. "<p>Please set up an administrator account.</p>\n"
		. "<form name='setAdmin' method='POST'>\n"
		. "<input type='hidden' name='action' value='set_admin' />\n"
		. "<table noborder>\n"
		. "  <tr>\n"
		. "    <td><b>User name:</b></td>\n"
		. "    <td><input type='text' name='firstrun_adminuser' value='$adminuser'></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Password:</b></td>\n"
		. "    <td><input type='password' name='firstrun_adminpass'></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b>Confirm:</b></td>\n"
		. "    <td><input type='password' name='firstrun_adminpass2'></td>\n"
		. "  </tr>\n"
		. "  <tr>\n"
		. "    <td><b></b></td>\n"
		. "    <td><input type='submit' value='Create admin account'></td>\n"
		. "  </tr>\n"
		. "</table>"		
		. "</form>\n"
		. "";

	//---------------------------------------------------------------------------------------------
	//	handle POSTs
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('action', $_POST)) {
		//-----------------------------------------------------------------------------------------
		//	set MySQL root user and password
		//-----------------------------------------------------------------------------------------

		if ('set_dbr' == $_POST['action']) {
			$kapenta->registry->set('firstrun.dbr.user', $_POST['firstrun_dbr_user']);
			$kapenta->registry->set('firstrun.dbr.password', $_POST['firstrun_dbr_password']);
		}

		//-----------------------------------------------------------------------------------------
		//	set MySQL database details
		//-----------------------------------------------------------------------------------------

		if ('set_mysql' == $_POST['action']) {
			$kapenta->registry->set('firstrun.dbr.host', $_POST['kapenta_db_host']);
			$kapenta->registry->set('firstrun.dbr.name', $_POST['kapenta_db_name']);
			$kapenta->registry->set('firstrun.dbr.user', $_POST['kapenta_db_user']);
			$kapenta->registry->set('firstrun.dbr.password', $_POST['kapenta_db_password']);

			// use this form from now on
			$kapenta->registry->set('firstrun.dbr.created', 'yes');
			$kapenta->registry->set('firstrun.dbr.granted', 'yes');
			echo "<div class='chatmessageblack'>Changing to manual database settings...</div>\n";
		}

		//-----------------------------------------------------------------------------------------
		//	set recovery password
		//-----------------------------------------------------------------------------------------
	
		if ('set_recovery' == $_POST['action']) {
			if ($_POST['recovery1'] != $_POST['recovery2']) {
				echo "<div class='chatmessagered'>Recovery passwords do not match.</div>";
				echo "<div class='chatmessageblack'>$frmRecoveryPass</div>";
				die();
			} else {
				$kapenta->registry->set('firstrun.recovery', $_POST['recovery1']);
				$kapenta->registry->set('kapenta.recoverypassword', sha1($_POST['recovery1']));
			}
		}

		//-----------------------------------------------------------------------------------------
		//	set administrator account
		//-----------------------------------------------------------------------------------------
	
		if ('set_admin' == $_POST['action']) {
			echo "<div class='chatmessageblack'>Setting admin details...</div>\n";
			if ($_POST['firstrun_adminpass'] != $_POST['firstrun_adminpass2']) {
				echo "<div class='chatmessagered'>Admin passwords do not match.</div>";
				echo "<div class='chatmessageblack'>$frmAdminUser</div>";
				die();
			}

			if (3 > strlen($_POST['firstrun_adminuser'])) {
				echo "<div class='chatmessagered'>Please enter an admin user.</div>";
				echo "<div class='chatmessageblack'>$frmAdminUser</div>";
				die();			
			}

			$kapenta->registry->set('firstrun.adminuser', $_POST['firstrun_adminuser']);
			$kapenta->registry->set('firstrun.adminpass', $_POST['firstrun_adminpass']);

		}

	}
		
	//---------------------------------------------------------------------------------------------
	//	install database (xampp settings)
	//---------------------------------------------------------------------------------------------
	if ('yes' !== $kapenta->registry->get('firstrun.dbr.installed')) {
	
		$db->host = $kapenta->registry->get('firstrun.dbr.host');
		$db->user = $kapenta->registry->get('firstrun.dbr.user');;
		$db->pass = $kapenta->registry->get('firstrun.dbr.password');;
		$db->name = $kapenta->registry->get('firstrun.dbr.name');
	
		//-----------------------------------------------------------------------------------------
		//	create the database itself
		//-----------------------------------------------------------------------------------------
	
		if ('yes' !== $kapenta->registry->get('firstrun.dbr.created')) {
			
			$check = $dba->create($db->name);
			
			$msg = "Creating database `awareNet` using default XAMPP root user... ";
			if (true == $check) {
				$msg .= "<b>OK</b>";
				$kapenta->registry->set('firstrun.dbr.created', 'yes');
				echo "<div class='chatmessagegreen'>$msg</div>";
			
				if (true == $dba->dbExists($db->name)) {
					$kapenta->registry->set('firstrun.dbr.created', 'yes');
				}
			
			} else {
				$msg .= "<b>FAIL</b>.";
				echo "<div class='chatmessagered'>$msg</div>";
				echo "<div class='chatmessageblack'>$frmMySQLRoot</div>";
				echo "<div class='chatmessageblack'>$frmMySQLDetail</div>";
				die();
				
			}
			
		}
	
		//-----------------------------------------------------------------------------------------
		//	create database user
		//-----------------------------------------------------------------------------------------
	
		if ('yes' !== $kapenta->registry->get('firstrun.dbr.granted')) {
			$newUser = $kapenta->registry->get('firstrun.db.user');
			$newPass = $kapenta->registry->get('firstrun.db.password');
			
			$sql = ''
				. "GRANT ALL ON " . $db->name . ".* "
				. "TO '$newUser'@'localhost' IDENTIFIED BY '$newPass' ";
		
			$msg = "Creating new database user for use by awareNet... ";
		
			$check = $kapenta->db->query($sql);
			if (true == $check) {
				$msg .= "<b>OK</b>.";
				$kapenta->registry->set('firstrun.dbr.granted', 'yes');
				$kapenta->registry->set('kapenta.db.user', $newUser);
				$kapenta->registry->set('kapenta.db.password', $newPass);
				$kapenta->registry->set('kapenta.db.host', 'localhost');
				$kapenta->registry->set('kapenta.db.name', $db->name);
				echo "<div class='chatmessagegreen'>$msg</div>";
				
			} else {
				$msg .= "<b>FAIL</b>.";
				echo "<div class='chatmessagered'>$msg</div>";
				echo "<div class='chatmessageblack'>$sql</div>";
				die();
			}
		}	
	}

	//---------------------------------------------------------------------------------------------
	//	copy db credentials
	//---------------------------------------------------------------------------------------------
	$kapenta->registry->set('kapenta.db.host', $kapenta->registry->get('firstrun.dbr.host'));
	$kapenta->registry->set('kapenta.db.name', $kapenta->registry->get('firstrun.dbr.name'));
	$kapenta->registry->set('kapenta.db.user', $kapenta->registry->get('firstrun.dbr.user'));
	$kapenta->registry->set('kapenta.db.password', $kapenta->registry->get('firstrun.dbr.password'));

	//---------------------------------------------------------------------------------------------
	//	create and delete canary table
	//---------------------------------------------------------------------------------------------

	$dbSchema = array();
	$dbSchema['module'] = 'canary';
	$dbSchema['model'] = 'canary_canary';

	//table columns
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(33)',
		'refModule' => 'VARCHAR(50)',
		'refModel' => 'VARCHAR(50)',
		'refUID' => 'VARCHAR(33)',
		'createdOn' => 'DATETIME',
		'createdBy' => 'VARCHAR(33)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(33)',
		'shared' => 'VARCHAR(3)',
		'revision' => 'BIGINT(20)',
		'alias' => 'VARCHAR(255)' );

	$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

	$dbSchema['nodiff'] = array(
		'UID',
		'editedOn',
		'editedBy',
		'weight'
	);

	echo "<div class='chatmessageblack'>Testing database permissions...</div>\n";

	if (true == $dba->createTable($dbSchema)) {
		echo "<div class='chatmessagegreen'>Table created successfully... </div>";

		$check = $kapenta->db->query("drop table canary_canary");
		if (false == $check) {
			echo ''
			 . "<div class='chatmessagered'>Could not remove test table (canary_canary)</div><br/>";
		} else {
			echo "<div class='chatmessagegreen'>Table deleted successfully... </div><br/>";
		}

	} else {
		echo ''
		 . "<div class='chatmessagered'>Could not create database tables, please check settings:"
		 . "<br/>\n$frmMySQLDetail</div>\n";
		die();
	}

	echo "<br/>\n";


	//---------------------------------------------------------------------------------------------
	//	set up recovery password
	//---------------------------------------------------------------------------------------------

	if ('' == $kapenta->registry->get('firstrun.recovery')) {
		echo "<div class='chatmessageblack'>$frmRecoveryPass</div>\n";
		die();
	} else {
		$kapenta->registry->set('kapenta.recoverypassword', sha1($kapenta->registry->get('firstrun.recovery')));
		echo "<div class='chatmessagegreen'>Recovery password set.</div>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	set up admin user
	//---------------------------------------------------------------------------------------------

	if ('' == $kapenta->registry->get('firstrun.adminuser')) {
		echo "<div class='chatmessageblack'>$frmAdminUser</div>\n";
		die();		
	} else {
		echo "<div class='chatmessagegreen'>Admin details set.</div>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	run all module install scripts
	//---------------------------------------------------------------------------------------------
	
	echo "<h2>Installing Modules</h2>";
	$mods = $kapenta->listModules();
	$failures = false;	

	foreach($mods as $moduleName) {
		$msg = "<b>Installing module: $moduleName </b><br/>";
		$installScript = 'modules/' . $moduleName . '/inc/install.inc.php';
		if (true == $kapenta->fs->exists($installScript)) {
			$msg .= "Install script: $installScript<br/>";
			$installFn = $moduleName . "_install_module";
			
			require_once($kapenta->installPath . $installScript);
			if (true == function_exists($installFn)) {
				$msg .= "Call: $installFn<br/>";
				
				$report = $installFn();
				$msg .= "<hr/><br/><div class='chatmessageblack'>$report</div><br/>";

				if (false !== strpos($report, "<span class='ajaxerror'>failed</span>")) {
					$failures = true;
				}

			} else {
				$msg .= "Missing install function, please install manually.<br/>";
			}
			
		} else {
			$msg .= "No install script for this module.<br/>";
			
		}
		echo "<div class='chatmessageblack'>$msg</div><br/>\n"; flush();
	}
	
	if (true == $failures) {
		echo ''
		 . "<div class='chatmessagered'>Some modules could not be installed.
			Please review messages before continuing.</div>";
		die();
	}

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	
	$kapenta->registry->set('firstrun.complete', 'yes');
	echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]", '');
	
?>
