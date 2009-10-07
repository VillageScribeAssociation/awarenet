<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	editform
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit

function moblog_editform($args) {
	if (authHas('moblog', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new moblog($args['raUID']);
	if ($model->data['UID'] == '') { return ''; }
	$ext = $model->extArray();
	$ext['contentJs64'] = base64EncodeJs('contentJs64', $ext['content']);
	return replaceLabels($ext, loadBlock('modules/moblog/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
