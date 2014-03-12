<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of comment [string]

function comments_summary($args) {
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$model = new Comments_Comment($args['UID']);
	if (false == $model->loaded) { return '(not found)'; }
	if (false == $kapenta->user->authHas('comments', 'comment_comment', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
