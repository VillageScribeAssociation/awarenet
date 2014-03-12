<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	simple utility to caclulate and return a SHA1 hash
//--------------------------------------------------------------------------------------------------

function live_WebShell_sha1($args) {
		global $kapenta;
		global $kapenta;
		global $shell;

	$html = '';							//%	return value [string]
	$mode = 'sha1';						//%	operation [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	foreach ($args as $idx => $arg) {
		switch($arg) {
			case '-h':			$mode = 'help';		break;
			case '-s':			$mode = 'sha1';		break;
			case '--help':		$mode = 'help';		break;
			case '--sha1':		$mode = 'sha1';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'sha1':
			foreach($args as $arg) {
				if (('-s' != $arg) && ('--sha1' != $arg)) {
					$html .= "sha1(". htmlentities($arg) ."):<br/><tt>". sha1($arg) ."</tt><br/>";
				}
			}
			break;	//..............................................................................

		case 'help':
			$html = live_WebShell_sha1_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the live.sha1 command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function live_WebShell_sha1_help($short = false) {
	if (true == $short) { return "Calculate sha1 hashes."; }

	$html = "
	<b>usage: live.sha1 [-h|-s] [<i>value</i>]</b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--sha1|-s] <i>value</i></b><br/>
	Calculate sha1 hash.<br/>
	<br/>
	";

	return $html;
}


?>
