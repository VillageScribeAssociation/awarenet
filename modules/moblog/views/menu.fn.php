<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	moblog submenu
//--------------------------------------------------------------------------------------------------

function moblog_menu($args) { return loadBlock('modules/moblog/views/menu.block.php'); }

//--------------------------------------------------------------------------------------------------

?>
