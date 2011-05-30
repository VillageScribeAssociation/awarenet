<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums at a specific school (null string for general forums)
//--------------------------------------------------------------------------------------------------
//arg: school - UID of a school (not recordAlias) [string]

function forums_summarylist($args) {
	global $db, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('school', $args)) { return ''; }
	if (false == $user->authHas('forums', 'forums_board', 'show')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load boards from database
	//----------------------------------------------------------------------------------------------
	$conditions = array("school='" . $db->addMarkup($args['school']) . "'");
	$range = $db->loadRange('forums_board', '*', $conditions, 'weight DESC');
	//$sql = "select * from Forums_Board "
	//	 . "where school='" . $db->addMarkup($args['school']) . "' "
	//	 . "order by weight DESC";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { $html = "(no forums as yet)<br/>\n"; }

	foreach($range as $row) { 
		$html .= "[[:forums::summary::raUID=" . $row['UID'] . ":]]\n"
			   . "[[:forums::showthreads::num=10::forumUID=" . $row['UID'] . ":]]";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
