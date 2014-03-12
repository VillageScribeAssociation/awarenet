<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	print aliases table
//--------------------------------------------------------------------------------------------------

function live_WebShell_cd($args) {
		global $kapenta;
		global $kapenta;
		global $shell;

	$mode = 'change';		//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'change':
			if (false == array_key_exists(0, $args)) {
				$shell->set('cwd', '~/');
				$html .= "Changed to document root.";	
			} else {
				$html .= $shell->chdir($args[0]);
			}			
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_cd_help();
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

function live_WebShell_cd_help($short = false) {
	if (true == $short) { return "Change current working directory."; }

	$html = "
	<b>usage: live.cd <i>[\"directory\"]</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
