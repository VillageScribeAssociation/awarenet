<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu
//--------------------------------------------------------------------------------------------------

function sync_menu($args) {
	global $theme;
 return $theme->loadBlock('modules/sync/views/menu.block.php'); }

//--------------------------------------------------------------------------------------------------

?>