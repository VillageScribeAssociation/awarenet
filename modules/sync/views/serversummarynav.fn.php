<?

	require_once($installPath . 'modules/sync/models/servers.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or groups entry
// * $args['postUID'] = overrides raUID

function sync_serversummarynav($args) {
	if (array_key_exists('postUID', $args) == true) { $args['raUID'] = $args['postUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new sync($args['raUID']);	
	return replaceLabels($model->extArray(), loadBlock('modules/sync/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>