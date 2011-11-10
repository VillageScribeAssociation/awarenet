<?

	require_once('core/kregistry.class.php');

//--------------------------------------------------------------------------------------------------
//*	utility to create kapenta registry
//--------------------------------------------------------------------------------------------------
//+	NOTE: expects 'pass' value in GET, matching recovery password, will also accept 'reload' value
//+	to reload setup.inc.php, and 'set' to set values.
//+	
//+	Sending recovery password as a $_GET var is awful, TODO: fix this

	//----------------------------------------------------------------------------------------------
	//	initialize registry and check password
	//----------------------------------------------------------------------------------------------
	session_start();
	$registry = new KRegistry();
	$auth = false;

	$recoveryPass = $registry->get('kapenta.recoverypassword');
	$userPass = '';

	if ((true == array_key_exists('action', $_POST)) && ('logout' == $_POST['action'])) {
		$_SESSION['regsetup_pass'] = '';
	}

	if ((true == array_key_exists('action', $_POST)) && ('login' == $_POST['action'])) {
		if (true == array_key_exists('pass', $_POST)) { $userPass = $_POST['pass']; }
		$_SESSION['regsetup_pass'] = $userPass;
	}

	if (true == array_key_exists('regsetup_pass', $_SESSION)) { 
		$userPass = $_SESSION['regsetup_pass']; 
	}

	//----------------------------------------------------------------------------------------------
	//	show login form if not authorized
	//----------------------------------------------------------------------------------------------
	$header = "<html>
		<head>
			<link href='themes/clockface/css/default.css' 
				rel='stylesheet' type='text/css' />
		</head>
		<body>
		<h1>Kapenta Registry Setup</h1>
	";

	$footer = "</body></html>";

	echo $header;

	if (sha1($userPass) == $recoveryPass) { $auth = true; }
	if ('' == $recoveryPass) { $auth = true; }

	if (false == $auth) { 
		$loginForm = "
			<h2>Please log in.</h2>
			<form name='pwForm' method='POST'>
				<input type='hidden' name='action' value='login' />
				<b>Kapenta Recovery Password:</b> <input type='password' name='pass' />
				<input type='submit' />
			</form><hr/><br/>
			";
		die($loginForm . $footer); 
	}

	//----------------------------------------------------------------------------------------------
	//	load if setup.inc.php if requested and it exists - save values to the registry
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('action', $_POST)) && ('reload' == $_POST['action'])) {
		if (true == file_exists('setup.inc.php')) { 
			require_once('setup.inc.php'); 
			echo "Reloading setup.inc.php...<br/>\n";
			
			$varMap = array(
				'installPath'	=>	'kapenta.installpath',
				'serverPath'	=>	'kapenta.serverpath',
				'dbType'		=>	'kapenta.db.type',
				'dbHost'		=>	'kapenta.db.host',
				'dbName'		=>	'kapenta.db.name',
				'dbUser'		=>	'kapenta.db.user',
				'dbPass'		=>	'kapenta.db.password',
				'defaultModule'	=>	'kapenta.modules.default',
				'useBlockCache'	=>	'kapenta.blockcache.enabled',
				'websiteName'	=>	'kapenta.sitename',
				'defaultTheme'	=>	'kapenta.themes.default',
				'logLevel'		=>	'kapenta.loglevel',
				'cronInterval'	=>	'kapenta.cron.interval',
				'hostInterface'	=>	'kapenta.network.interface',
				'proxyEnabled'	=>	'kapenta.proxy.enabled',
				'proxyAddress'	=>	'kapenta.proxy.address',
				'proxyPort'		=>	'kapenta.proxy.port',
				'proxyUser'		=>	'kapenta.proxy.user',
				'proxyPass'		=>	'kapenta.proxy.pass',
				'rsaKeySize'	=>	'kapenta.rsa.keysize',
				'rsaPublicKey'	=>	'kapenta.rsa.publickey',
				'rsaPrivateKey'	=>	'kapenta.rsa.privatekey'
			);

			foreach ($varMap as $varName => $regkey) {
				if (true == isset($$varName)) { $registry->set($regkey, $$varName); }
				echo "setting $regkey ...<br/>\n";
			}

		} else {
			echo "setup.inc.php not found.<br/>\n";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	set a key
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('add' == $_POST['action'])) {
		$key = (array_key_exists('key', $_POST)) ? $_POST['key'] : '';
		$value = (array_key_exists('value', $_POST)) ? $_POST['value'] : '';

		//echo "setting $key to $value ...<br/>";

		if (('kapenta.recoverypassword' == $key) && ('' != $value)) { $value = sha1($value); }

		if ('' != $key) {
			$registry->set($key, $value);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load all registry files
	//----------------------------------------------------------------------------------------------
	$prefixes = $registry->listFiles();

	echo "<b>Jump to:</b> ";
	foreach($prefixes as $prefix) { echo "<a href='#$prefix'>$prefix</a>, "; }
	echo "<br/><br/>";

	//----------------------------------------------------------------------------------------------
	//	show 'add key' form
	//----------------------------------------------------------------------------------------------
	$addForm = "
		<form name='addForm' method='POST'>
			<input type='hidden' name='action' value='add' />
			<b>key:</b> <input type='text' name='key' />
			<b>value:</b> <input type='text' name='value' />
			<input type='submit' value='Add Key &gt;&gt;' />
		</form><br/>
	";

	//----------------------------------------------------------------------------------------------
	//	show 'reload' form
	//----------------------------------------------------------------------------------------------
	$reloadForm = "
		<form name='reloadForm' method='POST'>
			<input type='hidden' name='action' value='reload' />
			<input type='submit' value='Reload setup.inc.php &gt;&gt;' />
		</form><hr/><br/>
	";

	//----------------------------------------------------------------------------------------------
	//	show 'log out' form
	//----------------------------------------------------------------------------------------------
	$logOutForm = "
		<form name='logoutForm' method='POST'>
			<input type='hidden' name='action' value='logout' />
			<input type='submit' value='Log out &gt;&gt;' />
		</form><hr/><br/>
	";

	echo $logOutForm;

	//----------------------------------------------------------------------------------------------
	//	show keys and controls
	//----------------------------------------------------------------------------------------------

	foreach($prefixes as $prefix) {
		echo "<h2><a name='$prefix'>" . $prefix . "</a></h2>\n";
		echo $addForm;
		echo $registry->toHtml($prefix);
		echo $reloadForm;
	}	

	echo $logOutForm;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	echo $footer;
?>
