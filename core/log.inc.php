<?

//--------------------------------------------------------------------------------------------------
//*	log events, errors, pageviews and hotlinks
//--------------------------------------------------------------------------------------------------
//+
//--------------------------------------------------------------------------------------------------
//+	System Events
//--------------------------------------------------------------------------------------------------
//+
//+	Logs are stored as .php files to prevent direct access from a web browser.
//+
//+	System events are logged according to granularity:
//+	(0) no logging of system events
//+	(1) user logins, records created and saved
//+	(2) + page views, function and block calls
//+	(3) + return values
//+	
//+	Log format for event and error log:
//+	<event>
//+		<datetime>1258791458</datetime>
//+		<session>1258791458</session>
//+		<ip>127.0.0.1</ip>
//+		<system>users</system>
//+		<user>32462483244</user>
//+		<function>authenticate</function>
//+		<msg>plaintext</msg>
//+	</event>
//+
//+	Log data are escaped with htmlentities()
//+	Further information about remotehost, form variables, etc can be retrieved from page view log.
//+
//--------------------------------------------------------------------------------------------------
//+	Page Views
//--------------------------------------------------------------------------------------------------
//+
//+	Page view logs are only stored for full (outer) page views, not iframes, AJAX, etc

//--------------------------------------------------------------------------------------------------
//|	record current page view
//--------------------------------------------------------------------------------------------------

function logPageView() {
	global $installPath;
	global $page;
	global $user;

	$fileName = $installPath . 'data/log/' . date("y-m-d") . "-pageview.php";
	if (file_exists($fileName) == false) { makeEmptyLog($fileName);	}

	$entry = "<entry>\n"
		. "\t<timstamp>" . time() . "</timestamp>\n"
		. "\t<mysqltime>" . mysql_datetime() . "</mysqltime>\n"
		. "\t<user>" . $user->data['username'] . "</user>\n"
		. "\t<remotehost>" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "</remotehost>\n"
		. "\t<remoteip>" . $_SERVER['REMOTE_ADDR'] . "</remoteip>\n"
		. "\t<request>" . $_SERVER['REQUEST_URI'] . "</request>\n"
		. "\t<referrer>" . $_SERVER['HTTP_REFERER'] . "</referrer>\n"
		. "\t<useragent>" . $_SERVER['HTTP_USER_AGENT'] . "</useragent>\n"
		. "\t<uid>" . $page->UID . "</uid>\n"
		. "</entry>\n";

	$result = filePutContents($fileName, $entry, 'a+');

	notifyChannel('admin-syspagelog', 'add', base64_encode($entry));
	$entry = mysql_datetime() . " - " . $user->data['username'] . ' - ' . $_SERVER['REQUEST_URI'];
	notifyChannel('admin-syspagelogsimple', 'add', base64_encode($entry));

	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	create an empty log file TODO: use filePutContents
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute fileName [string]

function makeEmptyLog($fileName) {
	$defaultLog = "<?\n" .
	$defaultLog .= "\tinclude '../../setup.inc.php';\n";
	$defaultLog .= "\tinclude \$installPath . 'core/core.inc.php';\n";
	$defaultLog .= "\tlogErr('log', 'eventLog', 'direct access by browser');\n";
	$defaultLog .= "\tdo404();\n";
	$defaultLog .= "?>\n\n";

	$fH = fopen($fileName, 'w+');
	fwrite($fH, $defaultLog);
	fclose($fH);
}

//--------------------------------------------------------------------------------------------------
//|	record a system event
//--------------------------------------------------------------------------------------------------
//arg: log - log name [string]
//arg: subsystem - subsystem name/label [string]
//arg: fn - function name [string]
//arg: msg - message to log [string]
//returns: true on success or false on failure [bool]

function logEvent($log, $subsystem, $fn, $msg) {
	global $installPath;

	//----------------------------------------------------------------------------------------------
	//	create new log files as necessary
	//----------------------------------------------------------------------------------------------

	$fileName = $installPath . 'data/log/' . date("y-m-d") . '-' . $log . '.log.php';
	if (file_exists($fileName) == false) { makeEmptyLog($fileName);	}

	//----------------------------------------------------------------------------------------------
	//	add a new entry to the log file
	//----------------------------------------------------------------------------------------------

	$entry = "<event>\n";
	$entry .= "\t<datetime>" . mysql_datetime() . "</datetime>\n";
	$entry .= "\t<session>" . $_SESSION['sUID'] . "</session>\n";
	$entry .= "\t<ip>" . $_SERVER['REMOTE_ADDR'] . "</ip>\n";
	$entry .= "\t<system>" . $subsystem . "</system>\n";
	$entry .= "\t<user>" . $_SESSION['sUserUID'] . "</user>\n";
	$entry .= "\t<function>" . $fn . "</function>\n";
	$entry .= "\t<msg>$msg</msg>\n";
	$entry .= "</event>\n";

	return filePutContents($fileName, $entry, 'a+');
}

//--------------------------------------------------------------------------------------------------
//|	record an ordinary system event
//--------------------------------------------------------------------------------------------------
//arg: granularity - level of detail (0-3) [int]
//arg: subsystem - subsystem name/label [string]
//arg: fn - function name [string]
//arg: msg - message to log [string]
//returns: true on success or false on failure [bool]

function logEv($granularity, $subsystem, $fn, $msg) {
	global $logLevel;
	if ($logLevel >= $granularity) { return logEvent('event', $subsystem, $fn, $msg); }
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	record an error
//--------------------------------------------------------------------------------------------------
//arg: subsystem - subsystem name/label [string]
//arg: fn - function name [string]
//arg: msg - message to log [string]
//returns: true on success or false on failure [bool]

function logErr($subsystem, $fn, $msg) { return logEvent('error', $subsystem, $fn, $msg); }

//--------------------------------------------------------------------------------------------------
//|	log sync activity
//--------------------------------------------------------------------------------------------------
//arg: msg - message to log [string]
//: this is a little overused due to development, needs to be trimmed out of a lot of places now
//: that the sync module is pretty stable.

function logSync($msg) {
	$fileName = $installPath . 'data/log/' . date("y-m-d") . '-sync.log.php';
	if (file_exists($fileName) == false) { makeEmptyLog($fileName);	}
	$msg = mysql_datetime() . " *******************************************************\n" . $msg;
	return filePutContents($fileName, $msg, 'a+');
}

?>
