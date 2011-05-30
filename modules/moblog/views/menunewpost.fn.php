<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	creates a submenu item for creating a new post, deprecated - TODO: remove or replace this
//--------------------------------------------------------------------------------------------------

function moblog_menunewpost($args) { 
	if ($user->authHas('moblog', 'moblog_post', 'edit', 'TODO:UIDHERE') == true) {
		return '[[:theme::submenu::label=New Post::link=/moblog/new/:]]';
	}
}

//--------------------------------------------------------------------------------------------------

?>
