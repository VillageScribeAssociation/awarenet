<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');

//-------------------------------------------------------------------------------------------------
//*	configure awareNet from Windows Installer
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check that this is not already complete
	//---------------------------------------------------------------------------------------------
	if ('yes' == $registry->get('firstrun.complete')) {
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
		if ('' == $registry->get($key)) { $registry->set($key, $value); }
	}
			
	//---------------------------------------------------------------------------------------------
	//	run as admin
	//---------------------------------------------------------------------------------------------
	$user->role = 'admin';
	echo $theme->expandBlocks("[[:theme::ifscrollheader:]]", '');	
	echo "<h1>Configuring awareNet</h1>";
	
	$dba = new KDBAdminDriver();
	
	//---------------------------------------------------------------------------------------------
	//	handle POSTs
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('action', $_POST)) {
		//-----------------------------------------------------------------------------------------
		//	set MySQL root user and password
		//-----------------------------------------------------------------------------------------

		if ('set_dbr' == $_POST['action']) {
			$registry->set('firstrun.dbr.user', $_POST['firstrun_dbr_user']);
			$registry->set('firstrun.dbr.password', $_POST['firstrun_dbr_password']);
		}
	
	}
	
	//---------------------------------------------------------------------------------------------
	//	define some forms
	//---------------------------------------------------------------------------------------------

	$frmMySQLRoot = '' 
		. "Please enter your MySQL root user and password to create `awarenet` database:<br/>\n"
		. "<br/>"
		. "<form name='frmSetDbr' method='POST'>\n"
		. "<input type='hidden' name='action' value='set_dbr' />"
		. "<b>Username:</b> <input type='text' name='firstrun_dbr_user' "
		 . "value='" . $registry->get('firstrun.dbr.user') . "'/>&nbsp; "
		. "<b>Password:</b> <input type='text' name='firstrun_dbr_password' "
		 . "value='" . $registry->get('firstrun.dbr.password') . "'/>&nbsp; "
		. "<input type='submit' value='Retry &gt;&gt;' />"
		. "</form><br/>\n"
		. "<small>Default values for XAMPP are 'root' and no password.</small>";

	$frmMySQLDetail = ''
		. "<p>Alternatively, if you already have a database set up, please set the details:"
		. "<form name='frmSetMySQL' method='POST'>"
		. "<input type='hidden' name='action' value='set_mysql' />"
		. "<table noborder width='100%'>"
		. "  <tr>\n"
		. "    <td><b>Database name:</b></td>"
		. "    <td><input type='text' name='kapenta.db.name'></td>"
		. "  </tr>"
		. "</table>"
		. "</form>";
	
	//---------------------------------------------------------------------------------------------
	//	install database (xampp settings)
	//---------------------------------------------------------------------------------------------
	if ('yes' !== $registry->get('firstrun.dbr.installed')) {
	
		$db->host = $registry->get('firstrun.dbr.host');
		$db->user = $registry->get('firstrun.dbr.user');;
		$db->pass = $registry->get('firstrun.dbr.password');;
		$db->name = $registry->get('firstrun.dbr.name');
	
		//-----------------------------------------------------------------------------------------
		//	create the database itself
		//-----------------------------------------------------------------------------------------
	
		if ('yes' !== $registry->get('firstrun.dbr.created')) {
			
			$check = $dba->create($db->name);
			
			$msg = "Creating database `awareNet` using default XAMPP root user... ";
			if (true == $check) {
				$msg .= "<b>OK</b>";
				$registry->set('firstrun.dbr.created', 'yes');
				echo "<div class='chatmessagegreen'>$msg</div>";
			
				if (true == $dba->dbExists($db->name)) {
					$registry->set('firstrun.dbr.created', 'yes');
				}
			
			} else {
				$msg .= "<b>FAIL</b>.";
				echo "<div class='chatmessagered'>$msg</div>";
				echo "<div class='chatmessageblack'>$frmMySQLRoot</div>";
				die();
				
			}
			
		}
	
		//-----------------------------------------------------------------------------------------
		//	create database user
		//-----------------------------------------------------------------------------------------
	
	
		if ('yes' !== $registry->get('firstrun.dbr.granted')) {
			$newUser = $registry->get('firstrun.db.user');
			$newPass = $registry->get('firstrun.db.password');
			
			$sql = ''
				. "GRANT ALL ON " . $db->name . ".* "
				. "TO '$newUser'@'localhost' IDENTIFIED BY '$newPass' ";
		
			$msg = "Creating new database user for use by awareNet... ";
		
			$check = $db->query($sql);
			if (true == $check) {
				$msg .= "<b>OK</b>.";
				$registry->set('firstrun.dbr.granted', 'yes');
				$registry->set('kapenta.db.user', $newUser);
				$registry->set('kapenta.db.password', $newPass);
				$registry->set('kapenta.db.host', 'localhost');
				$registry->set('kapenta.db.name', $db->name);
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
	//	run all module install scripts
	//---------------------------------------------------------------------------------------------
	
	echo "<h2>Installing Modules</h2>";
	$mods = $kapenta->listModules();
	
	foreach($mods as $moduleName) {
		$msg = "<b>Installing module: $moduleName </b><br/>";
		$installScript = 'modules/' . $moduleName . '/inc/install.inc.php';
		if (true == $kapenta->fileExists($installScript)) {
			$msg .= "Install script: $installScript<br/>";
			$installFn = $moduleName . "_install_module";
			
			require_once($kapenta->installPath . $installScript);
			if (true == function_exists($installFn)) {
				$msg .= "Call: $installFn<br/>";
				
				$report = $installFn();
				$msg .= "<hr/><br/><div class='chatmessageblack'>$report</div><br/>";
				
			} else {
				$msg .= "Missing install function, please install manually.<br/>";
			}
			
		} else {
			$msg .= "No install script for this module.<br/>";
			
		}
		echo "<div class='chatmessageblack'>$msg</div><br/>\n";
	}
	
	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	
	echo $theme->expandBlocks("[[:theme::ifscrollfooter:]]", '');
	
?>