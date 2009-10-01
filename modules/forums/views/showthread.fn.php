<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a thread
//--------------------------------------------------------------------------------------------------
// * $args['threadUID'] = UID of a forum thread

function forums_showthread($args) {
	// TODO: auth
	if (array_key_exists('threadUID', $args) == false) { return false; }
	$model = new ForumThread($args['threadUID']);
	$html = replaceLabels($model->extArray(), loadBlock('modules/forums/views/showthread.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>