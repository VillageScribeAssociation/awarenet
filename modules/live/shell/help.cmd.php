<?

//--------------------------------------------------------------------------------------------------
//|	display built-in help for web shell commands
//--------------------------------------------------------------------------------------------------

function live_WebShell_help($args) {
	global $kapenta, $theme;
	$mode = 'list';		//%	operation mode [string]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists(0, $args)) {
		switch($args[0]) {
			case '-h': 			$mode = 'help';		break;
			case '-s':			$mode = 'short';	break;
			case '-l':			$mode = 'long';		break;
			case '--help':		$mode = 'help';		break;
			case '--short':		$mode = 'short';	break;
			case '--long':		$mode = 'long';		break;
			default:			$mode = 'long';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------

	switch($mode) {
		case 'list':
			$table = array(array('Command', 'Description'));
			$modules = $kapenta->listModules();

			foreach($modules as $module) {
				$list = $kapenta->listFiles('modules/' . $module . '/shell/', '.cmd.php'); 
				foreach($list as $fileName) {
					$cmd = $module . '.' . str_replace('.cmd.php', '', $fileName);
					$short = $kapenta->shellExecCmd('live.help', array('-s', $cmd));				
					$table[] = array($cmd, $short);
				}
			}

			$html = $theme->arrayToHtmlTable($table, true, true) . '<!-- cmd.ok() -->';
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_help_help();
			break;	//..............................................................................

		case 'long':
			$cmd = '';
			if (true == array_key_exists(0, $args)) { $cmd = $args[0]; }
			if (true == array_key_exists(1, $args)) { $cmd = $args[1]; }
			if ('' == $cmd) { return live_WebShell_help_help(); }
			if (false == $kapenta->shellCmdExists($cmd)) {
				$aliases = new Live_CmdAliases();
				$cmd = $aliases->find($cmd);
				if (false == $kapenta->shellCmdExists($cmd)) {
					return "<span class='ajaxwarn'>Help failed: unknown command: ". $cmd ."</span>";
				} else {
					$html .= "<i>canonical name: $cmd</i><br/>";
				}
			}
			$html .= $kapenta->shellCmdHelp($cmd, false);
			break;	//..............................................................................

		case 'short':
			$cmd = '';
			if (true == array_key_exists(1, $args)) { $cmd = $args[1]; }
			if ('' == $cmd) { return live_WebShell_help_help(); }
			if (false == $kapenta->shellCmdExists($cmd)) {
				$aliases = new Live_CmdAliases();
				$cmd = $aliases->find($cmd);
				if (false == $kapenta->shellCmdExists($cmd)) {
					return "<span class='ajaxwarn'>Help failed: unknown command: ". $cmd ."</span>";
				} else {
					$html .= "<i>canonical name: $cmd</i><br/>";
				}
			}
			$html .= $kapenta->shellCmdHelp($cmd, true);
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.help command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_help_help($short = false) {
	if (true == $short) { return "Displays shell help / manpages."; }

	$html = "
	<b>usage: live.help <i>[--short|-s] [command]</i></b>
	<p>Displays help page for a given command, or list of commands if none given.</p>
	";

	return $html;
}

?>
