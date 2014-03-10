<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	temporary administrative command to do shell_exec
//--------------------------------------------------------------------------------------------------

function admin_WebShell_testcurl($args) {
		global $kapenta;
		global $user;
		global $shell;
		global $utils;

	$mode = 'exec';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $mode = 'noauth'; }
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }


	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'exec':
			$raw = $utils->curlGet('http://www.google.com/robots.txt');
			$html = "test curl get:<hr/><pre>$raw</pre>";
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_testcurl_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.cat command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_testcurl_help($short = false) {
	if (true == $short) { return "Test cURL wrapper."; }

	$html = "
	<b>usage: admin.testcurl <i>[url]</i></b><br/>
	Downloads a file via cURL and displays it in the console.  Used for testing proxy settings, etc.
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
