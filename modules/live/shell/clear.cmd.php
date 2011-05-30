<?

//--------------------------------------------------------------------------------------------------
//|	clear the shell history
//--------------------------------------------------------------------------------------------------

function live_WebShell_clear($args) {
	$html = "Clearing history... <!-- kshellwindow.clearHistory() -->";

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.clear command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_clear_help($short = false) {
	if (true == $short) { return 'Clear the display and shell history.'; }
	$html = "
	<b>usage: live.clear</b><br/>
	Clears the current shell's display and history.<br/>
	";
	return $html;
}

?>
