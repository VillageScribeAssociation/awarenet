<?

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu
//--------------------------------------------------------------------------------------------------

function sync_menu($args) { return loadBlock('modules/sync/views/menu.block.php'); }

//--------------------------------------------------------------------------------------------------

?>
