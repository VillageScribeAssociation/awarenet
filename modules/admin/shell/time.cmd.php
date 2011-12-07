<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	display kapenta network time
//--------------------------------------------------------------------------------------------------

function admin_WebShell_time($args) {
	global $kapenta;
	global $user;
	global $shell;

	$mode = 'show';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $mode = 'noauth'; }
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'show':
			$check = $kapenta->datetime($kapenta->strtotime($kapenta->datetime()));
			$html .= ''
			 . "Current datetime: " . $kapenta->datetime() . "<br/>\n"
			 . "Current timestamp: " . $kapenta->time() . "<br/>\n"
			 . "Converted back: " . $check . "<br/>\n";
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_time_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.time command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_time_help($short = false) {
	if (true == $short) { return "Display kapenta network time."; }

	$html = "
	<b>usage: admin.time</b><br/>
	Displays current Kapenta p2p network time.
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
