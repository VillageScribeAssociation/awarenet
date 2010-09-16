<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing the abstract
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]

function projects_editabstractform($args) {
	global $theme;

	global $user;
	if ($user->authHas('projects', 'Projects_Project', 'edit', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Projects_Project($args['raUID']);
	if ($model->isMember($user->UID) == false) { return false; }
	$ext = $model->extArray();
	$ext['abstractJs64'] = base64EncodeJs('abstractJs64', $ext['abstract']);
	return $theme->replaceLabels($ext, $theme->loadBlock('modules/projects/views/editabstractform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>