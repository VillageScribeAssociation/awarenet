<?php

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the users and blog posts
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function moblog_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of Code_Package object
	//----------------------------------------------------------------------------------------------
	if ('code_package' == strtolower($refModel)) {
		$model = new Code_Package($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................


		}
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Code_File object
	//----------------------------------------------------------------------------------------------
	if ('code_file' == strtolower($refModel)) {
		$model = new Code_File($UID);					// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'comitter':	
				// relationship exists if user is a comitter of this project
				$package = new Code_Package($model->package);
				if (false == $package->loaded) { return false; }
				if (true == $package->hasPrivilege($user->UID, 'commit')) { return true; }
				break;	//..........................................................................

		}
	}

	return false;	
}

?>
