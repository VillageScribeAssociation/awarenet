<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//arg: groupUID - overrides raUID [string]
// TODO: stop using [[groups::image::...::]]

function groups_summarynav($args) {
	global $user, $theme;
	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (array_key_exists('groupUID', $args) == true) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the group
	//----------------------------------------------------------------------------------------------
	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('groups', 'groups_group', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/groups/views/summarynav.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

