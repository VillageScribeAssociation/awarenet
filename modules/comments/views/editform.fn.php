<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	editform
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of post to edit

function comments_editform($args) {
	if (authHas('comments', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new comment($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/comments/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>