<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a peer server record [string]

function sync_serversummary($args) {
	global $theme;

	if ('admin' != $user->role) { return ''; }
	if (array_key_exists('UID', $args)) {
		$model = new Sync_Notice($args['UID']);
		$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/sync/summary.block.php'));
		return $html;
	}
}

//--------------------------------------------------------------------------------------------------

?>