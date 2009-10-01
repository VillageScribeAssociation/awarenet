<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary
//--------------------------------------------------------------------------------------------------

function moblog_summary($args) {
	if (authHas('moblog', 'view', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new moblog($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/moblog/views/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>