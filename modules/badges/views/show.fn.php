<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');
	require_once($kapenta->installPath . 'modules/badges/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a badge
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Badges_Badge object [string]
//opt: badgeUID - replaces raUID if present [string]

function badges_show($args) {
	global $theme;
	$html = ''; 		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('badgeUID', $args)) { $args['raUID'] == $args['badgeUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Badges_Badge($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/badges/views/show.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
