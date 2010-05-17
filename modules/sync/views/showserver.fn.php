<?

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a peer server record [string]

function sync_showserver($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new sync($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/sync/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
