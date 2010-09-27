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
	global $kapenta, $session;
	$session->msgAdmin("deprecated: logPageView => \$kapenta->logPageView()", 'bug');
	$result = $kapenta->logPageView();
	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	create an empty log file TODO: use filePutContents
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute fileName [string]

function makeEmptyLog($fileName) {
	global $kapenta, $session;
	$session->msgAdmin("deprecated: makeEmptyLog => \$kapenta->makeEmptyLog()", 'bug');
	$result = $kapenta->makeEmptyLog($fileName);
	return $result;
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
	global $kapenta, $session;
	$session->msgAdmin("deprecated: logEvent(...) => \$kapenta->logEvent(...)", 'bug');
	$result = $kapenta->logEvent($log, $subsystem, $fn, $msg);
	return $result;
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
	global $kapenta, $session;
	$session->msgAdmin("deprecated: logEv(...) => \$kapenta->logEv(...)", 'bug');
	$result = $kapenta->logEv($granularity, $subsystem, $fn, $msg);
	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	record an error
//--------------------------------------------------------------------------------------------------
//arg: subsystem - subsystem name/label [string]
//arg: fn - function name [string]
//arg: msg - message to log [string]
//returns: true on success or false on failure [bool]

function logErr($subsystem, $fn, $msg) { 
	global $kapenta, $session;
	$session->msgAdmin("deprecated: logErr(...) => \$kapenta->logErr(...)", 'bug');
	$result = $kapenta->logErr($subsystem, $fn, $msg);
	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	log sync activity
//--------------------------------------------------------------------------------------------------
//arg: msg - message to log [string]
//: this is a little overused due to development, needs to be trimmed out of a lot of places now
//: that the sync module is pretty stable.

function logSync($msg) {
	global $kapenta, $session;
	$session->msgAdmin("deprecated: logSync(...) => \$kapenta->logSync(...)", 'bug');
	$result = $kapenta->logSync($msg);
	return $result;
}

?>
