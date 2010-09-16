<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Announcements_Announcement object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('uid', $req->args)) { $page->do404(); }

	$model = new Announcements_Announcement($req->args['uid']);
	if (false == $model->loaded) { $page->do404('Announcement not found.'); }
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this announcement.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/announcements/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, );
	$session->msg($html, 'warn');
	$page->do302('announcements/' . $model->alias);

?>
