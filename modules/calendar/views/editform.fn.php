<?

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or calendar entry

function calendar_editform($args) {
	if (authHas('calendar', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Calendar($args['raUID']);
	$ext = $model->extArray();
	$ext['contentJs64'] = base64EncodeJs('contentJs64', $ext['content']);
	return replaceLabels($ext, loadBlock('modules/calendar/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------
?>
