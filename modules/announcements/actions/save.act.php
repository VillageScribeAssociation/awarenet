<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');
	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save an Announcements_Announcement object
//--------------------------------------------------------------------------------------------------
//TODO: update this with more generic (GENERATED) code

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) 
		{ $page->do404('Announcement not specified (UID)'); }
	
	$model = new Announcements_Announcement($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Announcement not found.'); }

	$authorised = false;

	//	group admins have the ability to post announcements from that group
	//if (('groups' == $refModule) && (true == $user->isGroupAdmin($refUID))) { $authorised = true; }

	//	other auth methods (admins can make any announcement they please)
	if (true == $user->authHas($model->refModule, $model->refModel, 'announcements-new', $model->refUID))
		{ $authorised = true; }

	if (false == $authorised) { $page->do403('You are not authorized to make announcements.'); }

	//----------------------------------------------------------------------------------------------
	//	save the object
	//----------------------------------------------------------------------------------------------
	$model->title = $utils->cleanString($_POST['title']);
	$model->content = $_POST['content'];						//TODO: sanitize this
	$ext = $model->extArray();
	$report = $model->save();

	if ('' != $report) {
		$session->msg('The announement could not be saved: ' . $report, 'bad');
		$page->do302('announcements/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	prepare notification // TODO: use an event for this
	//----------------------------------------------------------------------------------------------
	//TODO: this shoud be handled by an event	

	$mn = 'announcements_announcement';
	$title = "Announcement: " . $ext['title'];
	$content = $ext['summary'];
	$url = $ext['viewUrl'];

	if ($notifications->count('announcements', $mn, $model->UID) > 0) 
		{ $content = "Announcement has been changed.<br/>\n"; }

	$nUID = $notifications->create(
		'announcements', $mn, $model->UID, 'announcement_saved', $title, $content, $url
	);

	//----------------------------------------------------------------------------------------------
	//	add appropriate users and redirect back
	//----------------------------------------------------------------------------------------------
	if ('schools' == $model->refModule) { 
		$notifications->addSchool($nUID, $model->refUID); 

		$school = new Schools_School($model->refUID);
		if ((true == $school->loaded) && ('global' == $school->notifyAll)) {
			$notifications->addEveryone($nUID);
		}

	}
	if ('groups' == $model->refModule) { $notifications->addGroup($nUID, $model->refUID); }
	$notifications->addUser($nUID, $model->createdBy);	
	
	//$page->do302('announcements/' . $model->alias); 

?>
