<?

	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post to edit [string]

function announcements_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Announcement($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/announcements/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

