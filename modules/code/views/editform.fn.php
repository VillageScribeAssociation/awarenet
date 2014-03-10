<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or code entry

function code_editform($args) {
	if (authHas('code', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Code($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/code/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>