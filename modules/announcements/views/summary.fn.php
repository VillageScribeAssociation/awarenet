<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------

function announcements_summary($args) {
	global $theme;

	if ($user->authHas('announcements', 'Announcements_Announcement', 'show', 'TODO:UIDHERE') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new announcements($args['UID']);
		$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/announcements/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>