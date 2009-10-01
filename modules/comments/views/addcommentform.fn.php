<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to add new comments
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module which owns the record, required
// * $args['refUID'] = record which owns the comment, required
// * $args['return'] = page to return to, required
// * $args['invitation'] = text encouraging someone to leave a comment, optional

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