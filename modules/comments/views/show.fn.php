<?

	require_once($installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function comments_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new comments($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/comments/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

