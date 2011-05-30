<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the current user and groups they may belong to
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
	if ('groups_group' == $refModel) {
		$model = new Groups_Group($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {
			case 'creator':	
				// if the user greated the group
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'groupmember':
				// if the user is a member of this group
				$members = $model->getMembers();
				foreach($members as $row) 
					{ if ($row['userUID'] == $userUID) { return true; }	}

				break;	//..........................................................................

			case 'groupadmin':
				// if the user is an administrator of this group
				$members = $model->getMembers();
				foreach($members as $row) { 
					if (($row['userUID'] == $userUID) && ('yes' == $row['admin'])) { return true; }	
				}

				break;

		}

	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Groups_Membership object go here, if any come up
	//----------------------------------------------------------------------------------------------
	//placeholder

	return false;	
}

?>
