<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	add a reply form
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]

function forums_replyform($args) {
	global $kapenta;
	global $theme;

	// TODO: auth

	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { return ''; }

	if (array_key_exists('threadUID', $args) == false) { return false; }
	$model = new Forums_Thread($args['threadUID']);
	$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/forums/views/replyform.block.php'));

	$html = $theme->ntb($html, 'Reply to this discussion', 'divReplyForm', 'hide');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
