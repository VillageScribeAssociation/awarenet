<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a group [string]

function groups_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Group($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/groups/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

