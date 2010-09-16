<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a group [string]

function groups_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Groups_Group($args['raUID']);
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/groups/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>