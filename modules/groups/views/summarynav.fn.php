<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//arg: groupUID - overrides raUID [string]
//arg: extra - any extra line or information to add to this summary [string]

function groups_summarynav($args) {
		global $user;
		global $theme;

	
	$extra = '';				//%	any other information to be added [string]
	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }
	
	if (true == array_key_exists('extra', $args)) { $extra = $args['extra']; }

	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('groups', 'groups_group', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/groups/views/summarynav.block.php');
	$labels = $model->extArray();
	$labels['groupUID'] = $labels['UID'];
	$labels['extra'] = $extra;
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

