<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	temporary administrative command to do shell_exec
//--------------------------------------------------------------------------------------------------

function admin_WebShell_bash($args) {
		global $kapenta;
		global $kapenta;
		global $shell;

	$mode = 'exec';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $mode = 'noauth'; }
	if (true == in_array('--help', $args)) { $mode = 'help'; }
	if (true == in_array('-h', $args)) { $mode = 'help'; }


	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'exec':
			$raw = htmlentities(shell_exec(implode(' ', $args)));
			$html = "shell_exec: " . implode(' ', $args) . "<hr/><pre>$raw</pre>";
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_bash_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.bash command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_bash_help($short = false) {
	if (true == $short) { return "Execute a command at the OS shell."; }

	$html = "
	<b>usage: admin.bash <i>shell command</i></b><br/>
	Executes a command at the OS console and returns results, if possible.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
