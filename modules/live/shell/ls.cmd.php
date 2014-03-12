<?

	require_once($kapenta->installPath . 'modules/live/inc/shellsession.class.php');
	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	print aliases table
//--------------------------------------------------------------------------------------------------

function live_WebShell_ls($args) {
		global $kapenta;
		global $kapenta;
		global $shell;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------

	if (false == isset($shell)) { $shell = new Live_ShellSession(); }
	
	switch($mode) {
		case 'list':
			if (false == array_key_exists(0, $args)) {
				$html .= $shell->ls();	
			} else {
				$html .= $shell->ls($args[0]);
			}			
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_ls_help();
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

function live_WebShell_ls_help($short = false) {
	if (true == $short) { return "List files in working directory."; }

	$html = "
	<b>usage: live.ls <i>[\"pattern\"]</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
