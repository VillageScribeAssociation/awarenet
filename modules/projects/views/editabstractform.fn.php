<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for editing the abstract
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or projects entry

function projects_editabstractform($args) {
	global $user;
	if (authHas('projects', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Project($args['raUID']);
	if ($model->isMember($user->data['UID']) == false) { return false; }
	$ext = $model->extArray();
	$ext['abstractJs64'] = base64EncodeJs('abstractJs64', $ext['abstract']);
	return replaceLabels($ext, loadBlock('modules/projects/views/editabstractform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
