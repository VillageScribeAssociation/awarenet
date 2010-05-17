<?

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a peer server record [string]

function sync_serversummary($args) {
	if (authHas('sync', 'view', '') == false) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new sync($args['UID']);
		$html = replaceLabels($model->extArray(), loadBlock('modules/sync/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>
