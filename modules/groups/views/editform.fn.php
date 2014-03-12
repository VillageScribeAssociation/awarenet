<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]

function groups_editform($args) {
		global $theme;
		global $kapenta;
		global $utils;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Groups_Group($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('groups', 'groups_group', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['description64'] = $utils->b64wrap($ext['description']);
	$block = $theme->loadBlock('modules/groups/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
