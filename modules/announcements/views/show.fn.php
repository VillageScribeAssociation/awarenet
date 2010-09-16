<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function announcements_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Announcements_Announcement($args['raUID']);
	if ($model->UID == '') { return false; }
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/announcements/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>