<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	prints the content of a file
//--------------------------------------------------------------------------------------------------

function live_WebShell_cat($args) {
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
	
	switch($mode) {
		case 'list':
			if (false == array_key_exists(0, $args)) { return live_WebShell_cat_help(); }			
			$fileName = substr($shell->get('cwd'), 1) . $args[0];
			if (false == $kapenta->fs->exists($fileName)) { return 'File not found: ' . $fileName; }
			$raw = $kapenta->fs->get($fileName, false, false);
			$raw = htmlentities($raw);
			$html = "<pre>" . $raw . "</pre>";
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
//|	manpage for the live.cat command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_cat_help($short = false) {
	if (true == $short) { return "List files in working directory."; }

	$html = "
	<b>usage: live.cat <i>[\"filename.ext\"]</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
