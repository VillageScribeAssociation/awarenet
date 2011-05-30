<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the current user and notifications
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function notifications_relationships($refModel, $UID, $relationship, $userUID) {
	global $user, $kapenta;

	//----------------------------------------------------------------------------------------------
	//	get the user we want to know about
	//----------------------------------------------------------------------------------------------	
	$refUser = $user;
	if ($userUID != $refUser->UID) { 
		$refUser = new Users_User($userUID);
		if (false == $refUser->loaded) { return false; }
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Notifications objects
	//----------------------------------------------------------------------------------------------
	if ('notifications_notification' == $refModel) {
		$model = new Notifications_Notification($UID);		// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {

			case 'creator':	
				// if the user created this notification
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'member':
				// if the notification was sent to this user
				if (true == $model->hasMember($refUser->UID)) { return true; }
				break;	//..........................................................................

		}

	}

	return false;	
}

?>
