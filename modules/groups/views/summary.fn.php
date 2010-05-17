<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]

function groups_summary($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$g = new Group(sqlMarkup($args['raUID']));	
	return replaceLabels($g->extArray(), loadBlock('modules/groups/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

