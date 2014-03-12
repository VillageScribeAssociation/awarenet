<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a comment [string]

function comments_summarynav($args) {
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new Comments_Comment($args['UID']);
	if (false == $model->loaded) { return '(not found)'; }
	if (false == $kapenta->user->authHas('comments', 'comment_comment', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/summarynav.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
