<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	simple utility to make and return a brand new UID
//--------------------------------------------------------------------------------------------------

function live_WebShell_newuid($args) {
	global $kapenta, $user, $shell;
	$html = '';							//%	return value [string]
	$mode = 'uid';						//%	operation [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	foreach ($args as $idx => $arg) {
		switch($arg) {
			case '-h':			$mode = 'help';		break;
			case '-u':			$mode = 'uid';		break;
			case '--help':		$mode = 'help';		break;
			case '--uid':		$mode = 'uid';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'uid':
			$count = 1;
			foreach($args as $arg) {
				if (('-u' != $arg) && ('--uid' != $arg)) {
					$count = (int)$arg;
				}
			}

			$uids = array();
			$collisions = array();
			
			for ($i = 0; $i < $count; $i++) {
				$uid = $kapenta->createUID();
				if (false == in_array($uid, $uids)) { $uids[] = $uid; }
				else { $collisions[] = "colission: " . $uid; }
			}
			
			$html .= implode("<br/>\n", $uids);
			
			if (count($collisions) > 0) {
				$html .= ''
				 . "<span class='ajaxwarn'>"
				 . implode("<br/>\n", $collisions)
				 . "</span>";
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

function live_WebShell_newuid_help($short = false) {
	if (true == $short) { return "Make new UIDs."; }

	$html = "
	<b>usage: live.newuid [-h|-u] [<i>number</i>]</b><br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--uid|-u] <i>number</i></b><br/>
	Name <i>number</i> new UIDs.<br/>
	<br/>
	";

	return $html;
}


?>
