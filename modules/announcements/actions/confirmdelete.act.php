<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Announcements_Announcement object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $page->do404(); }

	$model = new Announcements_Announcement($kapenta->request->args['UID']);
	if (false == $model->loaded) { $page->do404('Announcement not found.'); }
	if (false == $user->authHas('announcements', 'announcements_announcement', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this announcement.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	$block = $theme->loadBlock('modules/announcements/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');
	$page->do302('announcements/' . $model->alias);

?>
