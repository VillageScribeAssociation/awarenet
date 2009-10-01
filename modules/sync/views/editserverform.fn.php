<?

	require_once($installPath . 'modules/sync/models/servers.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//	editform
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = UID of server record

function sync_editserverform($args) {
	if (authHas('sync', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Server($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	return replaceLabels($model->extArray(), loadBlock('modules/sync/views/editserverform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>