<?

	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------------------
//args: UID - UID of a static page [string]

function static_summary($args) {
	global $theme;

	if ($user->authHas('home', 'Home_Static', 'show', 'TODO:UIDHERE') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new StaticPage($args['UID']);
		$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/static/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------------------

?>