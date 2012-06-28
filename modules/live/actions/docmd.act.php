<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdinterpreter.class.php');
	require_once($kapenta->installPath . 'modules/live/inc/shellsession.class.php');

//--------------------------------------------------------------------------------------------------
//*	execute a web shell command
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	initialize shell
	//----------------------------------------------------------------------------------------------
	$shell = new Live_ShellSession();

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('cmd', $_POST)) { die('cmd not given'); }

	$raw = base64_decode($_POST['cmd']);

	$interpreter = new Live_CmdInterpreter($raw);

	$result = $kapenta->shellExecCmd($interpreter->cmd, $interpreter->arguments);

	$result = str_replace('%%serverPath%%', $kapenta->serverPath, $result);
	$result = str_replace('%%defaultTheme%%', $kapenta->defaultTheme, $result);

	echo $result;

?>
