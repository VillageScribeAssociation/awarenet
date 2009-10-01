<?

	require_once($installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	show
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of post to edit

function comments_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new comments($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/comments/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>