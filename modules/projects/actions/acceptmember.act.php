<?

//--------------------------------------------------------------------------------------------------
//	action to accept someone who has applied to join a project
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] == 'public') { do403(); }
	if ($request['ref'] == '') { do404(); }

	require_once($installPath . 'modules/projects/models/projects.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load the request
	//----------------------------------------------------------------------------------------------
	$sql = "select * from projectmembers where UID='" . sqlMarkup($request['ref']) . "'";

	$result = dbQuery($sql);
	if (dbNumRows($result) == 0) { do404(); }	// no such request

	$membership = dbFetchAssoc($result);
	$model = new Project($membership['projectUID']);

	//----------------------------------------------------------------------------------------------
	//	ensure that current user is admin of project
	//----------------------------------------------------------------------------------------------
	$sql = "select * from projectmembers "
		 . "where projectUID='" . $membership['projectUID'] . "' "
		 . "and userUID='" . $user->data['UID'] . "' and role='admin'";

	$result = dbQuery($sql);
	if (dbNumRows($result) == 0) { do403(); }	// not a project admin

	//----------------------------------------------------------------------------------------------
	//	authorised, notify current members of new addition
	//----------------------------------------------------------------------------------------------

	$newUser = new Users($membership['userUID']);

	$title = $newUser->getNameLink() . " is now a member of project " . $model->getLink();
	$content = "Added by " . $user->getNameLink() . ' on ' . mysql_datetime();
	
	notifyProject($membership['projectUID'], createUID(), $user->getName(), 
					$user->getUrl(), $title, $content, $model->getUrl(), '');

	//----------------------------------------------------------------------------------------------
	//	authorised, grant membership
	//----------------------------------------------------------------------------------------------
	$sql = "update projectmembers set role='member' where UID='" . $membership['UID'] . "'";	
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	authorised, notify new member that they are now on the project
	//----------------------------------------------------------------------------------------------

	$fromUrl = '%%serverPath%%users/profile/' . $user->data['recordAlias'];
	$title = "You have been added to project: " . $model->getLink();
	$content = "You were added by " . $user->getNameLink() . " and can now edit this project. " . 
				"Please be considerate of the contributions of other members.";

	notifyUser($membership['userUID'], createUID(), $user->getName(), 
				$fromUrl, $title, $content, $model->getUrl(), '');

	//----------------------------------------------------------------------------------------------
	//	return to project page
	//----------------------------------------------------------------------------------------------
	$_SESSION['sMessage'] .= "You have added " . $newUser->getNameLink() 
						   . " as a new member to this project.";

	do302('projects/' . $membership['projectUID']);

?>
