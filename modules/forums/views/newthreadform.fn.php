<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to create a new thread
//--------------------------------------------------------------------------------------------------
//arg: forumUID - UID of a forum [string]

function forums_newthreadform($args) {
	global $kapenta;
	global $theme;

	// TODO: auth
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { return ''; }
	if (array_key_exists('forumUID', $args) == false) { return false; }
	
	$model = new Forums_Board($args['forumUID']);
	$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/forums/views/newthreadform.block.php'));

	$html = $theme->ntb($html, 'Start A New Discussion', 'divNewThread', 'hide');	

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
