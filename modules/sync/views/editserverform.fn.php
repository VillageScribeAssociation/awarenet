<?

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing a peer record
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of server record [string]

function sync_editserverform($args) {
	if (authHas('sync', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Server($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/sync/views/editserverform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
