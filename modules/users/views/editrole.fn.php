<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Role object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Users_Role object [string]
//opt: UID - UID of a Users_Role object, overrides raUID [string]
//opt: roleUID - UID of a Users_Role object, overrides raUID [string]

function users_editrole($args) {
	global $user;
	global $theme;
	global $utils;
	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('roleUID', $args)) { $raUID = $args['roleUID']; }
	if ('' == $raUID) { return ''; }
	$model = new Users_Role($raUID);	//% the object we're editing [object:Users_Role]

	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('users', 'users_role', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/editrole.block.php');
	$labels = $model->extArray();
	$labels['description64'] = $utils->b64wrap($labels['description']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
