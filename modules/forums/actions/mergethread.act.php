<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	merge two forum threads into one
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('fromThread', $_POST)) { $page->do404('fromThread not POSTed'); }
	if (false == array_key_exists('toThread', $_POST)) { $page->do404('toThread not POSTed'); }

	$fromThread = new Forums_Thread($_POST['fromThread']);
	if (false == $fromThread->loaded) { $page->do404('No such fromThread.'); }	

	$toThread = new Forums_Thread($_POST['toThread']);
	if (false == $toThread->loaded) { $page->do404('No such toThread.'); }	

	if ($fromThread->UID == $toThread->UID) { $page->do404('Cannot merge thread with itself.'); }

	$msg = "<br/><div class='inlinequote'>Moved by " . $user->getNameLink()
		 . " on " . $db->datetime() . "</div>";

	//----------------------------------------------------------------------------------------------
	//	recast orignal thread as reply
	//----------------------------------------------------------------------------------------------
	//update Users_User set editedOn='2010-10-07 14:57:47' where UID='public';
	$newReply = new Forums_Reply();
	$newReply->forum = $toThread->board;
	$newReply->thread = $toThread->UID;
	$newReply->content = $fromThread->content . $msg;
	$newReply->createdOn = $fromThread->createdOn;
	$newReply->createdBy = $fromThread->createdBy;

	$report = $newReply->save();
	if ('' == $report) {
		$success = "Converted thread " . $fromThread->UID
				 . " (" . $fromThread->title . ") into a reply on "
				 . $toThread->UID . " (" . $toThread->title . ")"; 
		$session->msg($success, 'ok');
	} else {
		$failure = "Could not convert thread ". $fromThread->UID
				 . " (" . $fromThread->title . ") into a reply on " 
				 . $toThread->UID . " (" . $toThread->title . ")"; 
		$session->msg($failure, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	add replies of fromThread to toThread
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "thread='" . $db->addMarkup($fromThread->UID) . "'";
	
	$range = $db->loadRange('forums_reply', '*', $conditions);

	if (0 == count($range)) { $session->msg('Thread has no replies, none to move.', 'info'); }

	foreach($range as $row) {
		$model = new Forums_Reply($row['UID']);
		$model->thread = $toThread->UID;
		$model->forum = $toThread->board;
		$model->content .= $msg;

		$report = $model->save();
		if ('' == $report) {
			$success = "Moved thread reply " . $model->UID
					 . " by [[:users::name::userUID=" . $model->createdBy . ":]] to thread "
					 . $toThread->UID . "(" . $toThread->title . ")"; 
			$session->msg($success, 'ok');

		} else {
			$failure = "Could not move reply " . $model->UID
					 . " by [[:users::name::userUID=" . $model->createdBy . ":]]"; 
			$session->msg($failure, 'bad');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	delete fromThread
	//----------------------------------------------------------------------------------------------

	$fromThread->delete();

	//----------------------------------------------------------------------------------------------
	//	update postcount on toThread
	//----------------------------------------------------------------------------------------------

	$toThread->save();

	//----------------------------------------------------------------------------------------------
	//	redirect to merged thread
	//----------------------------------------------------------------------------------------------

	$page->do302('forums/showthread/' . $toThread->alias);

?>
