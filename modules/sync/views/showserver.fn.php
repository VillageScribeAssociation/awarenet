<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a peer server record [string]

function sync_showserver($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Sync_Notice($args['raUID']);
	if ($model->UID == '') { return false; }
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/sync/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>