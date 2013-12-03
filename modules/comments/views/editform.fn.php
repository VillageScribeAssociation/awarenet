<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	editform
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of comment to edit [string]
//TODO: fix up this legacy code

function comments_editform($args) {
	global $theme;
	global $user;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return 'raUID not given'; }
	$model = new Comments_Comment($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('comments', 'comment_comment', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/editform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
