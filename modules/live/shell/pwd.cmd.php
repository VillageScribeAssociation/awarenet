<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	print working directory
//--------------------------------------------------------------------------------------------------

function live_WebShell_pwd($args) {
	global $shell;
	$mode = 'display';			//%	operation [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'display':
			$html = $shell->get('cwd');			
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_pwd_help();
			break;			
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.pwd command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_pwd_help($short = false) {
	if (true == $short) { return "Print current working directory."; }

	$html = "
	<b>usage: live.pwd <i>[--help|-h]</i></b><br/>
	This command displays the current working directory, your position in the filesystem.
	For other environment variables see live.set.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
