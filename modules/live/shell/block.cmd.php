<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	render a block
//--------------------------------------------------------------------------------------------------

function live_WebShell_block($args) {
	global $kapenta, $user, $shell, $theme;

	$mode = 'show';			//%	operation [string]
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
		case 'show':
			if (false == array_key_exists(0, $args)) { return live_WebShell_block_help(); }			

			if ('-s' == $args[0]) { $args[0] = ''; }
			if ('--show' == $args[0]) { $args[0] = ''; }

			$html .= ''
			 . "<small>Evaluating: <tt>" . htmlentities(implode('', $args)) . "</tt></small><br/>\n"
			 . $theme->expandBlocks(implode('', $args), '');

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
//|	manpage for the live.block command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_block_help($short = false) {
	if (true == $short) { return "Render a kapenta block."; }

	$html = "
	<b>usage: live.block <i>[\"blocktag\"]</i></b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
