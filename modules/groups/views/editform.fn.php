<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]

function groups_editform($args) {
	if (authHas('groups', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Group($args['raUID']);
	$ext = $model->extArray();
	$ext['descriptionJs64'] = base64EncodeJs('descriptionJs64', $ext['description']);
	return replaceLabels($ext, loadBlock('modules/groups/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

