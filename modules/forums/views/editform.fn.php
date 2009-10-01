<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or forums entry

function forums_editform($args) {
	if (authHas('forums', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forum($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/forums/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>