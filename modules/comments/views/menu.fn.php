<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu
//--------------------------------------------------------------------------------------------------

function comments_menu($args) {
	global $theme;
 return $theme->loadBlock('modules/comments/menu.block.php'); }

//--------------------------------------------------------------------------------------------------

?>