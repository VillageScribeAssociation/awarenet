<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the users and image galleries
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function gallery_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of Gallery object
	//----------------------------------------------------------------------------------------------
	if ('Gallery_Gallery' == $refModel) {
		$model = new Gallery_Gallery($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................


		}
	}

	return false;	
}

?>
