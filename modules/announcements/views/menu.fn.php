<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	announcements module menu
//--------------------------------------------------------------------------------------------------

function announcements_menu($args) {
	global $theme;
	$html = $theme->loadBlock('modules/announcements/menu.block.php')
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
