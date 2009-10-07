<?

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	create form for editing an announcement record
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of post to edit

function announcements_editform($args) {
	if (authHas('announcements', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Announcement($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	$ext = $model->extArray();
	$ext['contentJs64'] = base64EncodeJs('contentJs64', $ext['content']);
	return replaceLabels($ext, loadBlock('modules/announcements/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
