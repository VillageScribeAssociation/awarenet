<?php

	require_once('core/kregistry.class.php');

//--------------------------------------------------------------------------------------------------
//	utility script to set awareNet's server path to currently assigned IP address and display
//--------------------------------------------------------------------------------------------------

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
		<h1>awareNet Laboratory Setup</h1>
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
		echo $loginForm . $footer;
		die();
	}

	//----------------------------------------------------------------------------------------------
	//	detect OS if not specified
	//----------------------------------------------------------------------------------------------

	if ('' == $registry->get('kapenta.os')) {

		switch (substr(strtolower(PHP_OS), 0, 3)) {
			case 'win':		$registry->set('kapenta.os', 'windows');			break;
			case 'lin':		$registry->set('kapenta.os', 'linux');				break;
			case 'fre':		$registry->set('kapenta.os', 'freebsd');			break;

			default:		$registry->set('kapenta.os', 'unix');
		}
	}

	echo "Host operating system: " . PHP_OS . "<br/>\n";

	//----------------------------------------------------------------------------------------------
	//	detect IP adddress
	//----------------------------------------------------------------------------------------------

	$serverPath = 'http://' . $_SERVER['SERVER_ADDR'] . '/';
	$comment = '';

	$badip = array('', '127.0.0.1', '127.0.1.1');

	if ('windows' == $registry->get('kapenta.os')) {
		//------------------------------------------------------------------------------------------
		//	on windows hosts try to use ipconfig to get ipaddress
		//------------------------------------------------------------------------------------------
		//	this assumes PHP is allowed to shell_exec

		$candidate_name = '';
		$candidate_ip = '';

		$raw = shell_exec('ipconfig /all');
		$comment = $raw;
		$lines = explode("\n", $raw);

		foreach($lines as $line) {
			$line = trim($line);

			if (('' !== $line) && (false !== strpos($line, 'Description'))) {
				$start = strpos($line, ':') + 1;
				$candidate_name = trim(substr($line, $start));
			}

			if (('' !== $line) && (false !== strpos($line, 'IP Address'))) {
				$start = strpos($line, ':') + 1;
				$candidate_ip = trim(substr($line, $start));
			}

			if (('' !== $line) && (false !== strpos($line, 'IPv4 Address'))) {
				$start = strpos($line, ':') + 1;
				$candidate_ip = trim(substr($line, $start));
				$candidate_ip = str_replace('(Preferred)', '', $candidate_ip);
			}

			if (false == in_array($candidate_ip, $badip)) {
				echo "Interface: " . $candidate_name . "<br/>";
				echo "Bound ip: " . $candidate_ip . "<br/>";
				$serverPath = 'http://' . $candidate_ip . '/';
				break;
			}

		}

	} else {
		//------------------------------------------------------------------------------------------
		//	on *nix hosts try to use ifconfig to get ipaddress
		//------------------------------------------------------------------------------------------
		//	note that this is tested only on Ubuntu, will need ifconfig location or alternative
		//	tools for other distros and operating systems

		$candidate_name = '';
		$candidate_ip = '';

		$raw = shell_exec('/sbin/ifconfig -a');
		$comment = $raw;
		$lines = explode("\n", $raw);

		foreach($lines as $line) {
			$line = trim($line);

			if (('' !== $line) && (false !== strpos($line, 'Link encap:'))) {
				//echo $line . "<br/>\n";
				$end = strpos($line, ' ');
				$candidate_name = substr($line, 0, $end);
			}

			if (('' !== $line) && (false !== strpos($line, 'inet addr:'))) {
				$start = strpos($line, 'inet addr:') + 10;
				$end = strpos($line, ' ', $start);
				$candidate_ip = substr($line, $start, $end - $start);
			}

			if (false == in_array($candidate_ip, $badip)) {
				echo "Interface: " . $candidate_name . "<br/>";
				echo "Bound ip: " . $candidate_ip . "<br/>";
				$serverPath = 'http://' . $candidate_ip . '/';
				break;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	set in registry
	//----------------------------------------------------------------------------------------------

	if (
		('' !== $serverPath) &&
		('http://127.0.0.1/' !== $serverPath) &&
		('http://127.0.1.1/' !== $serverPath) 			//	<-- more conditions here
	) {

		echo "setting kapenta.serverpath := $serverPath<br/>\n";
		$registry->set('kapenta.serverpath', $serverPath);
	}

	//----------------------------------------------------------------------------------------------
	//	display to user
	//----------------------------------------------------------------------------------------------

	echo ''
	 . "<br/><br/><br/><br/><br/>\n"
	 . "<xcenter>\n"
	 . "<font size='40'>awareNet URL:</font><br/>\n"
	 . "<font size='40'><a href='$serverPath'>$serverPath</a></font><br/>\n"
	 . "</xcenter>\n"
	 . "<br/><br/><br/><br/><br/>\n"
	 . "<pre>$comment</pre>\n"
	 . $footer;

	//----------------------------------------------------------------------------------------------
	//	clear the cache
	//----------------------------------------------------------------------------------------------

	$sql = "DELETE FROM `cache_entry`;";
	switch(strtolower($registry->get('db.driver'))) {

		case '':		//	deliberate fallthrough
		case 'mysql':

			include('./core/dbdriver/mysql.dbd.php');
			$db = new KDBDriver_MySQL();
			$db->query($sql);
			echo "<b>Clearing cache...</b>";
			flush();

			break;		//..........................................................................

		case 'sqlite':

			include('./core/dbdriver/sqlite.dbd.php');
			$db = new KDBDriver_SQLite();
			$db->query($sql);
			echo "<b>Clearing cache...</b>";
			flush();

			break;		//..........................................................................

		default:
			echo "<h2>Please clear cache.</h2>";
			break;		//..........................................................................

	}


?>
