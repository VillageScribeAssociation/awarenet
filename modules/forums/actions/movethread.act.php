<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//-------------------------------------------------------------------------------------------------
//*	move a thread from one forum to another
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check permissions and variables
	//---------------------------------------------------------------------------------------------
	$auth = false; 
	$allOk = true;

	if ('admin' == $user->role) { $auth = true; }
	if ('teacher' == $user->role) { $auth = true; }
	//TODO: standard permissions check

	if (false == array_key_exists('action', $_POST)) {
		$msg = 'No action specified.';
		$allOk = false;

	} else {
		if ($_POST['action'] != 'moveThread') {
			$msg = 'Unsupported action.';
			$allOk = false;
		}
	}

	if (false == array_key_exists('forum', $_POST)) {
		$msg = 'No forum specified.';
		$allOk = false;
	} else {
		if (false == $db->objectExists('forums_board', $_POST['forum'])) {
			$msg = 'Forum not found.';
			$allOk = false;
		}
	}

	if (false == array_key_exists('thread', $_POST)) {
		$msg = 'No thread specified.';
		$allOk = false;
	} else {
		if (false == $db->objectExists('forums_thread', $_POST['thread'])) {
			$msg = 'Thread not found.';
			$allOk = false;
		}
	}

	//---------------------------------------------------------------------------------------------
	//	...
	//---------------------------------------------------------------------------------------------

	if ( (true == $auth) && (true == $allOk) ) {
		//-----------------------------------------------------------------------------------------
		//	instruction is correct and user is authorized
		//-----------------------------------------------------------------------------------------
		$model = new Forums_Thread($_POST['thread']);
		$model->board = $_POST['forum'];
		$model->save();

		$session->msg("Thread moved: " . $model->title, 'ok');
		$page->do302('forums/showthread/' . $model->alias); 

	} else {
		//-----------------------------------------------------------------------------------------
		//	something went wrong
		//-----------------------------------------------------------------------------------------
		$session->msg("Could not move forum thread: $msg", 'bad');
		$page->do302('forums/');

	}

?>
