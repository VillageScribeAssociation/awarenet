<?

	require_once($kapenta->installPath . 'modules/live/models/mailbox.mod.php');

//--------------------------------------------------------------------------------------------------
//*	get all messages belonging to a page
//--------------------------------------------------------------------------------------------------
//+	reference should be a page UID (ID of rendered page in the browser)
//+
//+	optional argument: chatsince [datetime] may be POSTed, any messages for the current user since
//+	this time will be added to output

	//----------------------------------------------------------------------------------------------
	//	check reference, arguments and user
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { echo "ERROR: no UID given\n"; die(); }
	
	if ('public' != $user->role) {
		$pingArgs = array('user' => $user->UID, 'role' => $user->role);
		$kapenta->raiseEvent('*', 'live_ping', $pingArgs);
	}

	$model = new Live_Mailbox($req->ref, true);
	if (false == $model->loaded) { 
		//------------------------------------------------------------------------------------------
		//	no such mailbox, create it
		//------------------------------------------------------------------------------------------
		$model->pageUID = $req->ref;
		$model->userUID = $user->UID;
		echo "NEW: creating mailbox ID " . $model->UID . " for page " . $req->ref . "\n";
		$model->save();
	}

	//----------------------------------------------------------------------------------------------
	//	return any (block) messages
	//----------------------------------------------------------------------------------------------
	if ('' == $model->messages) {
		// empty mailbox
		echo "EMPTY: no messages\n";
	
	} else {
		echo $model->messages;
		$model->messages = '';
	}

	$model->lastChecked = $kapenta->time();
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	return any (chat) messages
	//----------------------------------------------------------------------------------------------
	
	if  (true == array_key_exists('chatsince', $_POST)) {
		//------------------------------------------------------------------------------------------
		//	check argument and user role
		//------------------------------------------------------------------------------------------
		if ('public' == $user->role) { $page->doXmlError('please log in.'); }	// no public users
		if ('banned' == $user->role) { $page->doXmlError('banned.'); }			// banhammered

		$uUID = $db->addMarkup($user->UID);
		$datetime = $_POST['chatsince'];
		if (('' == $datetime) || ('0' == $datetime) || ('undefined' == $datetime))
			{ $datetime = $db->datetime(time() - 1000000); }

		//------------------------------------------------------------------------------------------
		//	load any new messsages from the database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "createdOn > cast('" . $db->addMarkup($datetime) . "' as datetime)";
		$conditions[] = "ownerUID='" . $uUID . "'";
		$conditions[] = "state='new'";

		$range = $db->loadRange('live_chat', '*', $conditions, 'createdOn ASC');

		if (0 == count($range)) {
			echo "NOCHAT: no new messages ($datetime)\n";
		} else {
			foreach($range as $row) {
				echo 'chat:'
				  . base64_encode($row['UID']) . '|'
				  . base64_encode($row['fromUID']) . '|'
				  . base64_encode($row['toUID']) . '|'
				  . base64_encode($row['msg']) . '|'
				  . base64_encode($row['sent']) . '|'
				  . base64_encode($row['state']) . "|"
				  . base64_encode($row['createdOn']) . "\n";
			}
		}

	}

	//TODO: update UserLogin here

?>
