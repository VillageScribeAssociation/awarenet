<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Room object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('chat', 'chat_room', 'new')) {
		$page->do403('You are not authorized to create new Rooms.');
	}


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Room();
	
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':			$model->title = $value;				break;
			case 'description':		$model->description = $value;		break;
			case 'memberCount':		$model->memberCount = $value;		break;
			case 'emptyOn':			$model->emptyOn = $value;			break;
		}
	}

	$model->state = 'local';

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Room<br/>', 'ok');
		$page->do302('/chat/editroom/' . $model->UID);
	} else {
		$session->msg('Could not create new Room:<br/>' . $report);
		$page->do302('/chat/');
	}

?>
