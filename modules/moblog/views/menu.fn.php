<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu
//--------------------------------------------------------------------------------------------------

function moblog_menu($args) { return loadBlock('modules/moblog/views/menu.block.php'); }

function moblog_menunewpost($args) { 
	if (authHas('moblog', 'edit', '') == true) {
		return '[[:theme::submenu::label=New Post::link=/moblog/new/:]]';
	}
}

//--------------------------------------------------------------------------------------------------

?>