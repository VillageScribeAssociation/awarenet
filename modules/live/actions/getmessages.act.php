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
	if ('' == $kapenta->request->ref) { echo "ERROR: no UID given\n"; die(); }
	
	if ('public' != $kapenta->user->role) {
		$pingArgs = array('user' => $kapenta->user->UID, 'role' => $kapenta->user->role);
		$kapenta->raiseEvent('*', 'live_ping', $pingArgs);
	}

	$model = new Live_Mailbox($kapenta->request->ref, true);
	if (false == $model->loaded) { 
		//------------------------------------------------------------------------------------------
		//	no such mailbox, create it
		//------------------------------------------------------------------------------------------
		$model->pageUID = $kapenta->request->ref;
		$model->userUID = $kapenta->user->UID;
		echo "NEW: creating mailbox ID " . $model->UID . " for page " . $kapenta->request->ref . "\n";
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
		if ('public' == $kapenta->user->role) { $kapenta->page->doXmlError('please log in.'); }	// no public users
		if ('banned' == $kapenta->user->role) { $kapenta->page->doXmlError('banned.'); }			// banhammered

		$uUID = $kapenta->db->addMarkup($kapenta->user->UID);
		$datetime = $_POST['chatsince'];
		if (('' == $datetime) || ('0' == $datetime) || ('undefined' == $datetime))
			{ $datetime = $kapenta->db->datetime(time() - 1000000); }

		//------------------------------------------------------------------------------------------
		//	load any new messsages from the database
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "createdOn > cast('" . $kapenta->db->addMarkup($datetime) . "' as datetime)";
		$conditions[] = "ownerUID='" . $uUID . "'";
		$conditions[] = "state='new'";

		$range = $kapenta->db->loadRange('live_chat', '*', $conditions, 'createdOn ASC');

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
