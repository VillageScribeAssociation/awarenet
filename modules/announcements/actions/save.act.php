<?

//--------------------------------------------------------------------------------------------------
//	save an announcement record
//--------------------------------------------------------------------------------------------------

	if (array_key_exists('UID', $_POST)) { do404; }
	if (dbRecordExists('announcements', $_POST['UID']) == false) { do404(); }

	require_once($installPath . 'modules/announcements/models/announcements.mod.php');
	$model = new Announcement($_POST['UID']);

	$refModule = $model->data['refModule'];
	$refUID = $model->data['refUID'];
	$authorised = false;

	//----------------------------------------------------------------------------------------------
	//	group admins have the ability to post announcements from that group
	//----------------------------------------------------------------------------------------------
	if ($refModule == 'groups') {
		if ($user->isGroupAdmin($refUID) == true) { $authorised = true; }
	}

	//----------------------------------------------------------------------------------------------
	//	other auth methods (admins can make any announcement they please)
	//----------------------------------------------------------------------------------------------
	if (authHas($refModule, 'makeannouncements', '') == true) { $authorised = true; }

	//----------------------------------------------------------------------------------------------
	//	save the record
	//----------------------------------------------------------------------------------------------

	if ( ($authorised == true) 
	   AND (array_key_exists('action', $_POST))
	   AND ($_POST['action'] == 'saveRecord') 
	   AND (array_key_exists('UID', $_POST))
	   AND (dbRecordExists('announcements', sqlMarkup($_POST['UID']))) ) {

		$send = true;
		if ('sent' == $model->data['notifications']) { $send = false; }
	
		//------------------------------------------------------------------------------------------
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->data['title'] = $_POST['title'];
		$model->data['content'] = $_POST['content'];
		$ext = $model->extArray();
		$model->save();

		//------------------------------------------------------------------------------------------
		//	prepare notification
		//------------------------------------------------------------------------------------------
		
		$noticeUID = createUID();
		$from = $user->getName();
		$fromurl = $serverPath . 'users/profile/' . $ext['recordAlias'];
		$title = "Announcement: " . $ext['title'];
		$content = $ext['content'];
		if (false == $send) { $content = "Announcement has been changed.<br/>\n"; }

		//------------------------------------------------------------------------------------------
		//	add notifications and redirect back
		//------------------------------------------------------------------------------------------

		if ($refModule == 'schools') { 
			$schoolRa = raGetDefault('schools', $refUID);
			$url = $serverPath . 'schools/' . $schoolRa . '#announce' . $ext['UID'];
			$imgRow = imgGetHeaviest('schools', $refUID);
			$imgUID = '';
			if (false != $imgRow) { $imgUID = $imgRow['UID']; }

			notifySchool($refUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
			$link = $schoolRa . '#announce' . $model->data['UID'];
			do302('schools/' . $link); 
		}
		if ($refModule == 'groups') { 

			$groupRa = raGetDefault('groups', $refUID);
			$url = $serverPath . 'groups/' . $groupRa . '#announce' . $ext['UID'];
			$imgRow = imgGetHeaviest('groups', $refUID);
			$imgUID = '';
			if (false != $imgRow) { $imgUID = $imgRow['UID']; }

			notifyGroup($refUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
			$link = raGetDefault('groups', $refUID) . '#announce' . $model->data['UID'];
			do302('groups/' . $link); 
		}
		
	} else { 
		do404();
	}

?>
