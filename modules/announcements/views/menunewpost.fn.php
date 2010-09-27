<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	creates a submenu item for adding new announcements, deprecated.  TODO: get rid of this
//--------------------------------------------------------------------------------------------------

function announcements_menunewpost($args) { 
	if ($user->authHas('announcements', 'Announcements_Announcement', 'edit', 'TODO:UIDHERE') == true) {
		return '[[:theme::submenu::label=New Post::link=/announcements/new/:]]';
	}
}

//--------------------------------------------------------------------------------------------------

?>

