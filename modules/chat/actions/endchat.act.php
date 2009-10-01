<?

//--------------------------------------------------------------------------------------------------
//	user has closed a chat window, remove its contents from the queue
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public users can't chat
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] == 'public') { echo "Not logged in."; flush(); die(); }

	require_once($installPath . 'modules/chat/models/chat.mod.php');

	//----------------------------------------------------------------------------------------------
	//	remove messages from the queue
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('fromuid', $request['args']) == true) {
		$fromUID = $request['args']['fromuid'];
		$queue = new Chat($user->data['UID']);
		$queue->expandMessages();
		foreach ($queue->messages as $key => $msg) 
			{	if($msg['from'] == $fromUID) { $queue->messages[$key]['UID'] = ''; }	}

		$queue->collapseMessages();
		$queue->save();
	
		//------------------------------------------------------------------------------------------
		//	send javascript to close the calling iframe
		//------------------------------------------------------------------------------------------

		$html = "<html><head><title>close chat window</title>
			<script>
			window.parent.windowRemove('" . $fromUID . "');
			</script></head><body>nothing to see here</body>
			</html>";

		echo $html;

	}


?>
