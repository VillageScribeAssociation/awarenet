<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between images and users
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of a Images_Image object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function images_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of Images_Image object
	//----------------------------------------------------------------------------------------------
	if ('images_image' == strtolower($refModel)) {
		$model = new Images_Image($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		//TODO: other relationships here
		switch($relationship) {
			case 'creator':
				if ($refUser->UID == $model->createdBy) { return true; }
				break;
		}

	} else { $session->msgAdmin('Unknown model: ' . $refModel, 'bug'); }


	return false;	
}

?>
