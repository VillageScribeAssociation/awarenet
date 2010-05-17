<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums at current user's school (formatted for nav)
//--------------------------------------------------------------------------------------------------

function forums_atmyschoolnav($args) {
	global $user;
	return "[[:forums::summarylistnav::school=" . $user->data['school'] . ":]]";
}

//--------------------------------------------------------------------------------------------------

?>
