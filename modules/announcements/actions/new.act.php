<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new announcement
//--------------------------------------------------------------------------------------------------
//reqarg: refModule - name of a kapenta module [string]
//reqarg: refModel - type of object which may have announcements [string]
//reqarg: refUID - UID of object which may have announcements [string]

	//----------------------------------------------------------------------------------------------
	//	check request args and user permissions
	//----------------------------------------------------------------------------------------------
	
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('refModule not given'); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('refModel not given'); }
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('refUID not given.'); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('Unkonwn module.'); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('Unkonwn module.'); }

	if (false == $user->authHas($refModule, $refModel, 'announcements-add', $refUID)) {
		$page->do403('You are not aothorized to make new announcements.');
	}

	//----------------------------------------------------------------------------------------------
	//	OK then, create it
	//----------------------------------------------------------------------------------------------

	$model = new Announcements_Announcement();
	$model->title = 'Announcement ' . $kapenta->createUID();
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;
	$model->save();

	$session->msg('New announcement created.  Please complete the following form:', 'ok');
	$page->do302('announcements/edit/' . $model->UID);

?>
