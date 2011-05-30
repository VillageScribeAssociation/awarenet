<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing the abstract
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]

function projects_editabstractform($args) {
	global $theme, $user, $utils;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Projects_Project($args['raUID']);
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }
	//if (false == $model->isMember($user->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['abstractJs64'] = $utils->base64EncodeJs('abstractJs64', $ext['abstract']);
	$block = $theme->loadBlock('modules/projects/views/editabstractform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
