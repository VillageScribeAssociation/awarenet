<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete an Announcements_Announcement object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST))
		{ $page->do404('Announcement not specified (UID).'); }
    
	$model = new Announcements_Announcement($_POST['UID']);
	if (false == $user->authHas('announcements', 'announcements_announcement', 'delete', $model->UID))
		{ $page->do403('You are not authorzed to delete this announcement.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the announcement and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted announcement: " . $model->title);
	$page->do302('announcements/');

?>
