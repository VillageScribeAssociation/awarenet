<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	print aliases table
//--------------------------------------------------------------------------------------------------

function live_WebShell_set($args) {
		global $kapenta;
		global $user;
		global $shell;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	foreach ($args as $idx => $arg) {
		switch($arg) {
			case '-c':	$args[$idx] = '--clear';	break;
			case '-f':	$args[$idx] = '--force';	break;
			case '-h':	$args[$idx] = '--help';		break;
		}
	}

	if (true == in_array('--clear', $args)) {
		if ('admin' == $user->role) { $mode = 'clear'; } else { $mode = 'noauth'; }
	}

	if (true == in_array('--force', $args)) {
		if ('admin' == $user->role) { $mode = 'force'; } else { $mode = 'noauth'; }
	}

	if (true == in_array('--help', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			$html = $shell->toHtml();
			break;	//..............................................................................

		case 'clear':
			$html = "This method is not yet implemented.";
			break;	//..............................................................................

		case 'force':
			if (false == array_key_exists(1, $args)) { return 'No varname given'; }
			if (false == array_key_exists(2, $args)) { return 'No Value given'; }
			$key = $args[1];
			$val = $args[2];
			
			if (false == $shell->has($key)) 
				{ $html .= "Creating new environamnt variable: $key<br/>"; }

			$shell->set($key, $val);
			$html .= "Setting environment variable $key:<br/>$val";			
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_set_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.aliases command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_set_help($short = false) {
	if (true == $short) { return "Show and modify web shell environment variables."; }

	$html = "
	<b>usage: live.set <i>[mode] [key] [\"value\"]</i></b><br/>
	<br/>
	<b>[--clear|-c]</b><br/>
	Deletes all web shell environment variables.  Only administrators can do this.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--list|-l]</b><br/>
	Displays list of all web shell environment variables.  This is the default mode.<br/>
	<br/>
	";

	return $html;
}


?>
