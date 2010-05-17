<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add new comments
//--------------------------------------------------------------------------------------------------
//arg: refModule - module which owns the record, required [string]
//arg: refUID - record which owns the comment, required [string]
//arg: return - page to return to, required [string]
//opt: invitation - text encouraging someone to leave a comment, optional [string]

function comments_addcommentform($args) {
	$invitation = 'Add a comment';
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('return', $args) == false) { return false; }

	if (array_key_exists('invitation', $args) == true) { $invitation = $args['invitiation']; }	
	if (authHas($args['refModule'], 'comment', '') == false) { return false; }

	$labels = array();
	$labels['invitation'] = $invitation;
	$labels['refModule'] = $args['refModule'];
	$labels['refUID'] = $args['refUID'];
	$labels['return'] = $args['return'];

	return replaceLabels($labels, loadBlock('modules/comments/views/addcomment.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

