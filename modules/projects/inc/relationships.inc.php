<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	relationships between the users and objects maintained by the projects module
//--------------------------------------------------------------------------------------------------
//arg: refModel - type of object [string]
//arg: UID - UID of an object [string]
//arg: relationship - between this object and a user [string]
//arg: userUID - UID of the user related to this object [string]
//returns: true if the given relationship exists, otherwise false [bool]

function projects_relationships($refModel, $UID, $relationship, $userUID) {
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
	//	relationships of Projects_Project objects
	//----------------------------------------------------------------------------------------------
	if ('projects_project' == strtolower($refModel)) {
		$model = new Projects_Project($UID);					// try load the object
		if (false == $model->loaded) { return false; }			// check that we did

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'projectmember':
				// is user is a member of this project
				if (true == $model->memberships->hasMember($userUID)) { return true; }
				if (true == $model->memberships->hasAdmin($userUID)) { return true; }
				break;	//..........................................................................

			case 'projectadmin':
				// is user is an administrator of this project
				if (true == $model->memberships->hasAdmin($userUID)) { return true; }
				break;

		}
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Projects_Section objects
	//----------------------------------------------------------------------------------------------
	if ('projects_section' == strtolower($refModel)) {
		$model = new Projects_Section($UID);					// try load the object
		if (false == $model->loaded) { return false; }			// check that we did

		$project = new Projects_Project($model->projectUID);
		if (false == $project->loaded) { return false; }

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'projectmember':
				// is user is a member of this project
				if (true == $project->memberships->hasMember($userUID)) { return true; }
				if (true == $project->memberships->hasAdmin($userUID)) { return true; }
				break;	//..........................................................................

			case 'projectadmin':
				// is user is an administrator of this project
				if (true == $project->memberships->hasAdmin($userUID)) { return true; }
				break;

		}
	}

	//----------------------------------------------------------------------------------------------
	//	relationships of Revision object
	//----------------------------------------------------------------------------------------------
	if ('projects_revision' == strtolower($refModel)) {
		$model = new Projects_Revision($UID);				// try load the object
		if (false == $model->loaded) { return false; }		// check that we did

		$project = new Projects_Project($model->projectUID);
		if (false == $project->loaded) { return false; }

		switch($relationship) {
			case 'creator':	
				// relationship exists of user started this project
				if ($model->createdBy == $userUID) { return true; }
				break;	//..........................................................................

			case 'projectmember':
				// is user is a member of this project
				if (true == $project->memberships->hasMember($userUID)) { return true; }
				if (true == $project->memberships->hasAdmin($userUID)) { return true; }
				break;	//..........................................................................

			case 'projectadmin':
				// is user is an administrator of this project
				if (true == $project->memberships->hasAdmin($userUID)) { return true; }
				break;
		}
	}

	return false;	
}

?>
