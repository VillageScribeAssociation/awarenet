<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums at current user's school (formatted for nav)
//--------------------------------------------------------------------------------------------------

function forums_atmyschoolnav($args) {
	global $user;
	return "[[:forums::summarylistnav::school=" . $user->school . ":]]";
}

//--------------------------------------------------------------------------------------------------

?>
