<?

	require_once($kapenta->installPath . 'modules/notifications/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	had a notification in a user's stream
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and ownership
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Notification to hide not specified'); }

	$model = new Notifications_UserIndex($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Notification index not found'); }
	if ('admin' != $user->role) {
		if ($model->userUID != $user->UID) { $page->d043('Not your notification to hide.'); }
	}

	//----------------------------------------------------------------------------------------------
	//	hide notification and redirect back to user's feed
	//----------------------------------------------------------------------------------------------
	$model->status = 'hide';
	$report = $model->save();
	if ('' == $report) { $session->msg('Notification hidden.'); }
	$page->do302('notifications/');

?>
