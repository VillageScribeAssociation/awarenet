<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of comment [string]

function comments_summary($args) {
	global $theme;

	if ($user->authHas('comments', 'Comment_Comment', 'show', 'TODO:UIDHERE') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new Comments_Comment($args['UID']);
		$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/comments/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>