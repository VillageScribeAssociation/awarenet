<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to create a new thread
//--------------------------------------------------------------------------------------------------
// * $args['forumUID'] = UID of a forum

function forums_newthreadform($args) {
	// TODO: auth
	if (array_key_exists('forumUID', $args) == false) { return false; }
	
	$model = new Forum($args['forumUID']);
	$html = replaceLabels($model->extArray(), loadBlock('modules/forums/views/newthreadform.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>