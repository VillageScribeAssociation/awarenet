<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//|	web shell command for listing p2p downloads (files)
//--------------------------------------------------------------------------------------------------

function p2p_WebShell_downloads($args) {
		global $kapenta;
		global $user;
		global $shell;
		global $kapenta;

	$mode = 'list';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	switch($args[0]) {
		case '-h':		$mode = 'help';		break;
		case '-l':		$mode = 'list';		break;
		case '-w':		$mode = 'wget';		break;
		case '--list':	$mode = 'list';		break;
		case '--help':	$mode = 'help';		break;
		case '--wget':	$mode = 'wget';		break;
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'list':
			$range = $kapenta->db->loadRange('p2p_peer', '*', '');
			foreach($range as $item) {
				$html .= "<b>" . $item['name'] . " (" . $item['UID'] . ")</b><br/>\n";
				$downloads = new P2P_Downloads($item['UID']);
				foreach($downloads->members as $fileName) {
					$html .= $fileName . "<br/>\n";
				}
			}
			break;	//..............................................................................

		case 'wget':
			$range = $kapenta->db->loadRange('p2p_peer', '*', '');
			$html .= "<pre>\n";
			foreach($range as $item) {
				$html .= "echo '" . $item['name'] . " (" . $item['UID'] . ")'\n";
				$downloads = new P2P_Downloads($item['UID']);
				foreach($downloads->members as $fileName) {
					$html .= ''
					 . "wget --output-document=\"" . $kapenta->installPath . $fileName . "\" "
					 . "\"" . $item['url'] . $fileName . "\"\n";
				}
			}
			$html .= "</pre>";
			break;	//..............................................................................

		case 'help':
			$html = p2p_WebShell_downloads_help();
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

function p2p_WebShell_downloads_help($short = false) {
	if (true == $short) { return "Execute a command at the OS shell."; }

	$html = "
	<b>usage: p2p.downloads [-l|-w]</b><br/>
	Executes a command at the OS console and returns results, if possible.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--list|-l]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
