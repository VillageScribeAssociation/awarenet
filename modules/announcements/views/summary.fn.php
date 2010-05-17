<?

	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------

function announcements_summary($args) {
	if (authHas('announcements', 'show', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new announcements($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/announcements/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>

