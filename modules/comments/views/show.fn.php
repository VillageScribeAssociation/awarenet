<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function comments_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new comments($args['raUID']);
	if ($model->UID == '') { return false; }
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/comments/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>