<?

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu
//--------------------------------------------------------------------------------------------------

function announcements_menu($args) { return loadBlock('modules/announcements/menu.block.php'); }

function announcements_menunewpost($args) { 
	if (authHas('announcements', 'edit', '') == true) {
		return '[[:theme::submenu::label=New Post::link=/announcements/new/:]]';
	}
}

//--------------------------------------------------------------------------------------------------

?>