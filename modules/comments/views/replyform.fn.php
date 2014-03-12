<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	inline comment reply form
//--------------------------------------------------------------------------------------------------
//arg: parentUID - UID of a comments_comment obejct [string]

function comments_replyform($args) {
	global $theme;
	global $kapenta;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('parentUID', $args)) { return '(parent comment not specified)'; }	

	$model = new Comments_Comment($args['parentUID']);
	if (false == $model->loaded) { return '(parent comment not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/comments/views/replyform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}


?>
