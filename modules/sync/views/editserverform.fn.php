<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing a peer record
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of server record [string]

function sync_editserverform($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Sync_Server($args['raUID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/sync/views/editserverform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
