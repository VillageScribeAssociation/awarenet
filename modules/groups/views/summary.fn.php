<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]

function groups_summary($args) {
	global $db;

	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$g = new Groups_Group($db->addMarkup($args['raUID']));	
	return $theme->replaceLabels($g->extArray(), $theme->loadBlock('modules/groups/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>