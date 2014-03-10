<?
	
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to accept someone who has applied to join a project
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'acceptMember' [string]
//postarg: projectUID - UID of a Project_Project object [string]
//postarg: userUID - UID of a Users_User object who has requested to join project [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified'); }
	if ('acceptMember' != $_POST['action']) { $kapenta->page->do404('Action not recognized'); }
	if (false == array_key_exists('projectUID', $_POST)) { $kapenta->page->do404('Project not specified.'); }
	if (false == array_key_exists('userUID', $_POST)) { $kapenta->page->do404('User not specified.'); }

	$projectUID = $_POST['projectUID'];
	$userUID = $_POST['userUID'];

	//----------------------------------------------------------------------------------------------
	//	load the project and check permissions, request
	//----------------------------------------------------------------------------------------------
	$project = new Projects_Project($projectUID);
	if (false ==  $project->loaded) { $kapenta->page->do404('No such project.'); }	// no project
	if (false == $user->authHas('projects', 'projects_project', 'editmembers', $project->UID)) {
		$kapenta->page->do403();
	}

	if (false == $project->memberships->hasAsked($userUID)) { $kapenta->page->do404('Request not found.'); }

	//----------------------------------------------------------------------------------------------
	//	authorised, grant membership
	//----------------------------------------------------------------------------------------------
	$membershipUID = $project->memberships->getUID($userUID);
	if ('' == $membershipUID) { $kapenta->page->do404('Membership not found.'); }
	
	$model = new Projects_Membership($membershipUID);	
	if (false == $model->loaded) { $kapenta->page->do404('Could not load membership.'); }
	$model->role = 'member';
	$report = $model->save();

	if ('' != $report) { $kapenta->page->do404("Database error, could not grant membership:<br/>$report"); }

	//----------------------------------------------------------------------------------------------
	//	authorised, create notification
	//----------------------------------------------------------------------------------------------
	$refUID = $project->UID;
	$newName = $theme->expandBlocks('[[:users::name::userUID=' . $model->userUID . ':]]', '');
	$title = $newName . " is now a member of project '" . $project->title . "'";
	$content = "Request to join was accepted by " . $user->getNameLink();
	$url = '%%serverPath%%projects/' . $project->alias;

	$nUID = $notifications->create(
		'projects', 'projects_project', $refUID, 'projects_memberaccepted', $title, $content, $url
	);

	//----------------------------------------------------------------------------------------------
	//	add user and their friends to notification
	//----------------------------------------------------------------------------------------------
	$notifications->addUser($nUID, $user->UID);
	$notifications->addFriends($nUID, $user->UID);

	//----------------------------------------------------------------------------------------------
	//	add project members to notification
	//----------------------------------------------------------------------------------------------
	$members = $project->memberships->getMembers();
	foreach($members as $userUID => $role) { $notifications->addUser($nUID, $user->UID); }
	
	//----------------------------------------------------------------------------------------------
	//	return to project page
	//----------------------------------------------------------------------------------------------
	$nameBlock = '[[:users::namelink::userUID=' . $model->userUID . ':]]';
	$session->msg("You have added $nameBlock as a new member of this project.", 'ok');
	$kapenta->page->do302('projects/' . $project->alias);

?>
