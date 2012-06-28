<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the users and objects maintained by the forums module
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function forums_relationships($refModel, $UID, $relationship, $userUID) {
	global $user;

	//----------------------------------------------------------------------------------------------
	//	get the user we want to know about
	//----------------------------------------------------------------------------------------------	
	$refUser = $user;
	if ($userUID != $refUser->UID) { 
		$refUser = new Users_User($userUID);
		if (false == $refUser->loaded) { return false; }
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Forums_Board objects
	//----------------------------------------------------------------------------------------------
	if ('forums_board' == strtolower($refModel)) {
		$model = new Forums_Board($UID);						// try load the object
		if (false == $model->loaded) { return false; }			// check that we did

		switch($relationship) {
			case 'creator':	
				// relationship exists if user created this board
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'poster':
				//TODO: discover if this user has ever posted in this forum
				break;	//..........................................................................

			case 'moderator':
				//TODO: implement forum moderator
				break;
			//Rosa, Karen Lee Gilliers, Louise Featherstone
		}
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Forums_Thread objects
	//----------------------------------------------------------------------------------------------
	if ('forums_thread' == strtolower($refModel)) {
		$model = new Forums_Thread($UID);						// try load the object
		if (false == $model->loaded) { return false; }			// check that we did

		$board = new Forums_Board($model->board);
		if (false == $board->loaded) { return false; }

		switch($relationship) {
			case 'creator':	
				// relationship exists if user started this thread
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'poster':
				//TODO:	check if user has posted in this thread
				break;	//..........................................................................

			case 'moderator':
				//TODO: implement moderator list
				break;

		}
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Forums_Reply object
	//----------------------------------------------------------------------------------------------
	if ('forums_reply' == strtolower($refModel)) {
		$model = new Forums_Reply($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		$board = new Forums_Board($model->forum);			//	board this reply belongs to
		if (false == $board->loaded) { return false; }

		switch($relationship) {
			case 'creator':
				// relationship exists if user created this reply
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'poster':
				//TODO: check if user has posted in this thread
				break;	//..........................................................................

			case 'moderator':
				//TODO: implement moderator list
				break;	//..........................................................................
		}
	}

	return false;	
}

?>
