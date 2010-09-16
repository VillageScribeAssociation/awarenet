<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	moblog submenu
//--------------------------------------------------------------------------------------------------

function moblog_menu($args) { 
	global $theme;
	return $theme->loadBlock('modules/moblog/views/menu.block.php'); 
}

//--------------------------------------------------------------------------------------------------

?>
