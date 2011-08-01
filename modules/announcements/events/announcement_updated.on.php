<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an announcement has been updated
//-------------------------------------------------------------------------------------------------
//arg: refModule - module to which announcement is attached [string]
//arg: refModel - type of object to which announcement is attached [string]
//arg: refUID - UID of object to which announcement is attached [string]
//arg: UID - UID of Announcements_Announcement object [string]
//arg: title - title of announcement [string]
//arg: content - body of announcement (html) [string]
//
function announcements__cb_announcement_updated($args) {
	global $kapenta, $db, $user, $page, $notifications;

	$maxAge = 24 * 60 * 60;				//%	one day in seconds [int]

	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('title', $args)) { return false; }
	if (false == array_key_exists('content', $args)) { return false; }

	$model = new Announcements_Announcement($args['UID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	prepare notification
	//----------------------------------------------------------------------------------------------
	$title = "Announcement: " . $ext['title'];
	$content = $ext['summary'];
	$url = $ext['viewUrl'];

	// discover if a notification already exists for this event
	$nUID = $notifications->existsRecent(
		$model->refModule, 
		$model->refModel, 
		$model->refUID, '*', 'announcement_updated', $maxAge
	);

	// do not repeat content
	if ($notifications->count('announcements', 'announcements_announcement', $model->UID) > 0) {
		$content = "Announcement has been changed.<br/>\n";
	}

	// create new notification if none found
	if ('' == $nUID) {
		$nUID = $notifications->create(
			'announcements', 'announcements_announcement', $model->UID, 'announcement_updated', 
			$title, $content, $url
		);
	}

	//----------------------------------------------------------------------------------------------
	//	add appropriate users and redirect back
	//----------------------------------------------------------------------------------------------
	//TODO: this should really be handled by the school module
	if ('schools' == $model->refModule) { 
		$notifications->addSchool($nUID, $model->refUID); 

		$school = new Schools_School($model->refUID);
		if ((true == $school->loaded) && ('global' == $school->notifyAll)) {
			$notifications->addEveryone($nUID);
		}
	}

	//TODO: thsi should really be handled by the groups module
	if ('groups' == $model->refModule) { $notifications->addGroup($nUID, $model->refUID); }

	$notifications->addUser($nUID, $model->createdBy);	

	//---------------------------------------------------------------------------------------------
	//	pull triggers
	//---------------------------------------------------------------------------------------------
	if ('announcements' == $args['module']) {
		$page->doTrigger('announcements', 'announcement-any');
		$page->doTrigger('announcements', 'announcement-' . $args['UID']);
		if (true == array_key_exists('refUID', $args['data'])) {
			$kapenta->logLive('firing: ' . 'announcements-refUID-' . $args['data']['refUID']);
			$page->doTrigger('announcements', 'announcements-refUID-' . $args['data']['refUID']);
		}
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
