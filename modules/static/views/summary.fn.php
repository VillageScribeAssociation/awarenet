<?

	require_once($installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary
//--------------------------------------------------------------------------------------------------------------

function static_summary($args) {
	if (authHas('static', 'view', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new StaticPage($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/static/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------------------

?>