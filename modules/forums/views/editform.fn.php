<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or forums entry [string]

function forums_editform($args) {
	if (authHas('forums', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forum($args['raUID']);
	$ext = $model->extArray();
	$ext['descriptionJs64'] = base64EncodeJs('descriptionJs64', $ext['description']);
	return replaceLabels($ext, loadBlock('modules/forums/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

