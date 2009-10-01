<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	editform
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of post to edit

function moblog_editform($args) {
	if (authHas('moblog', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new moblog($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/moblog/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>