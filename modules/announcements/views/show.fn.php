<?

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	show
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of post to edit

function announcements_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Announcement($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/announcements/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>