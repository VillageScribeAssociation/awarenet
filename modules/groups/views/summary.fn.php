<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or groups entry

function groups_summary($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$g = new Group(sqlMarkup($args['raUID']));	
	return replaceLabels($g->extArray(), loadBlock('modules/groups/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>