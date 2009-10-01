<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	add a reply form
//--------------------------------------------------------------------------------------------------
// * $args['threadUID'] = UID of a forum thread

function forums_replyform($args) {
	// TODO: auth
	if (array_key_exists('threadUID', $args) == false) { return false; }
	$model = new ForumThread($args['threadUID']);
	$html = replaceLabels($model->extArray(), loadBlock('modules/forums/views/replyform.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>