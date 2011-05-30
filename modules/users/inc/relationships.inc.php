<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the current user, other users, friendships and roles
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function users_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of User object
	//----------------------------------------------------------------------------------------------
	if ('users_user' == $refModel) {
		$model = new Users_User($UID);						// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {

			case 'self':	
				// if the user is me! (permission to reset password, etc)
				if ($model->UID == $userUID) { return true; }
				break;	//..........................................................................

			case 'friendof':
				// if we're a friend of this user
				//TODO:
				break;

			case 'friendreqto':
				// if we've made  a friend request to this user
				//TODO:
				break;

			case 'friendreqto':
				// if we've made  a friend request to this user
				//TODO:
				break;

		}

	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Friendship object
	//----------------------------------------------------------------------------------------------
	if ('users_friendship' == $refModel) {
		$model = new Users_Friendship($UID);
		if (false == $model->loaded) { return false; }
	}

	return false;	
}

?>
