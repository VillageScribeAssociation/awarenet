<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or forums entry [string]

function forums_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forum($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/forums/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

