<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to an Notification object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403('Only admins can use this interface.'); }

	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('saveNotification' != $_POST['action']) { $page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed'); }

	$UID = $_POST['UID'];

	if (false == $user->authHas('notifications', 'notifications_notification', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Notification.'); }
	if (false == array_key_exists('refModule', $_POST))
		{ $page->do404('reference module not specified', true); }
	if (false == array_key_exists('refModel', $_POST))
		{ $page->do404('reference model not specified', true); }
	if (false == array_key_exists('refUID', $_POST))
		{ $page->do404('reference object UID not specified', true); }
	if (false == $kapenta->moduleExists($_POST['refModule']))
		{ $page->do404('specified module does not exist', true); }
	if (false == $kapenta->db->objectExists($_POST['refModel'], $_POST['refUID']))
		{ $page->do404('specified owner does not exist in database', true); }


	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	$model = new Notifications_Notification($UID);
	if (false == $model->loaded) { $page->do404("could not load Notification $UID");}

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'refmodule':	$model->refModule = $utils->cleanString($value); break;
			case 'refmodel':	$model->refModel = $utils->cleanString($value); break;
			case 'refuid':	$model->refUID = $utils->cleanString($value); break;
			case 'title':	$model->title = $utils->cleanString($value); break;
			case 'content':	$model->content = $value; break;		//TODO: sanitize this
			case 'refurl':	$model->refUrl = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Notification', 'ok'); }
	else { $session->msg('Could not save Notification:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('notifications/shownotification/' . $model->UID); }

?>
