<?

//--------------------------------------------------------------------------------------------------
//|	clear the shell history
//--------------------------------------------------------------------------------------------------

function live_WebShell_exit($args) {
	$html = "Shutting down... <!-- kshellwindow.exit() -->";

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.clear command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_exit_help($short = false) {
	if (true == $short) { return 'End shell session.'; }
	$html = "
	<b>usage: live.exit</b><br/>
	End the current shell session.<br/>
	";
	return $html;
}

?>
