<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Badge object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Badges_Badge object [string]
//opt: UID - UID of a Badges_Badge object, overrides raUID [string]
//opt: badgeUID - UID of a Badges_Badge object, overrides raUID [string]

function badges_editbadge($args) {
	global $user, $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('badgeUID', $args)) { $raUID = $args['badgeUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Badges_Badge($raUID);	//% the object we're editing [object:Badges_Badge]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('badges', 'Badges_Badge', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/badges/views/editbadge.block.php');
	$labels = $model->extArray();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
