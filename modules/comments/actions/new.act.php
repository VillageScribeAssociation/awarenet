<?

//--------------------------------------------------------------------------------------------------
//*	add a new announcements post
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('announcements', 'announcements_announcement', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }
	if (array_key_exists('refmodule', $req->args) == false) { $page->do403(); }
	if (array_key_exists('refuid', $req->args) == false) { $page->do403(); }

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

	$model = new Announcements_Announcement();
	$model->notifications = 'init';
	$model->title = '';
	$model->refModule = $req->args['refmodule'];
	$model->refUID = $req->args['refuid'];
	$model->save();

	$page->do302('announcements/edit/' . $model->UID);

?>
