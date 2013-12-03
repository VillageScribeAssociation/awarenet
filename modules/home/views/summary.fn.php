<?

	require_once($kapenta->installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//args: UID - UID of a static page [string]

function static_summary($args) {
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	$model = new StaticPage($args['UID']);
	if (false == $model->loaded) { return '(not found)'; }
	if (false == $user->authHas('home', 'home_static', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/static/summary.block.php')
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
