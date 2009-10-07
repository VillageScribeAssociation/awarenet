<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
//args: raUID - recordAlias or UID or schools entry

function schools_editform($args) {
	if (authHas('schools', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new School($args['raUID']);
	$ext = $model->extArray();
	$ext['descriptionJs64'] = base64EncodeJs('descriptionJs64', $ext['description']);
	return replaceLabels($ext, loadBlock('modules/schools/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
