<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	editform
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of comment to edit [string]

function comments_editform($args) {
	global $theme;

	if ($user->authHas('comments', 'Comment_Comment', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new comment($args['raUID']);
	if ($model->UID == '') { return false; }
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/comments/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>