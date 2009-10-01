<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['groupUID'] = overrides raUID

function groups_summarynav($args) {
	if (array_key_exists('groupUID', $args) == true) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$g = new Group(sqlMarkup($args['raUID']));	
	return replaceLabels($g->extArray(), loadBlock('modules/groups/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>