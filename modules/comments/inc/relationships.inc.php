<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between comments and users
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of a Comments_Comment object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function comments_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of Messages_Message object
	//----------------------------------------------------------------------------------------------
	if ('comments_comment' == strtolower($refModel)) {
		$model = new Comments_Comment($UID);				// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {
			case 'creator':
				if ($refUser->UID == $model->createdBy) { return true; }
				break;

			case 'owner':
				if ($refUser->UID == $model->owner) { return true; }
				break;

			case 'recipient':
				if ($refUser->UID == $model->toUID) { return true; }
				break;

			case 'sender':
				if ($refUser->UID == $model->fromUID) { return true; }
				break;
		}

	} else { $session->msgAdmin('Unknown model: ' . $refModel, 'bug'); }
	return false;	
}

?>
