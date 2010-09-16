<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create form for editing an announcement record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function announcements_editform($args) {
	global $theme;

	if ($user->authHas('announcements', 'Announcements_Announcement', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Announcements_Announcement($args['raUID']);
	if ($model->UID == '') { return false; }
	$ext = $model->extArray();
	$ext['contentJs64'] = base64EncodeJs('contentJs64', $ext['content']);
	return $theme->replaceLabels($ext, $theme->loadBlock('modules/announcements/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>