<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	remove a member from a project, return HTML snippet to browser JS client
//--------------------------------------------------------------------------------------------------
//+	a projectUID and userUID should be posted along with 'removeMember as the form action

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) 
		{ echo "<span class='ajaxerror'>Action not given.</span>"; die(); }

	if ('removeMember' != $_POST['action']) 
		{ echo "<span class='ajaxerror'>Action not recognized.</span>"; die(); }

	if (false == array_key_exists('userUID', $_POST)) 
		{ echo "<span class='ajaxerror'>User UID not given.</span>"; die(); }

	if (false == array_key_exists('projectUID', $_POST)) 
		{ echo "<span class='ajaxerror'>Project UID not given.</span>"; die(); }

	$userUID = $_POST['userUID'];					//%	UID of a Users_User object [string]
	$projectUID = $_POST['projectUID'];				//%	UID of a projects_project object [string]
	$role = 'member';								//%	placeholder for event arg [string]

	$model = new Projects_Project($_POST['projectUID']);
	if (false == $model->loaded) { echo "<span class='ajaxerror'>Unknown project.</span>"; die(); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	remove the member
	//----------------------------------------------------------------------------------------------
	if (false == $model->memberships->hasMember($userUID)) {
		echo "<span class='ajaxwarn'>This person is not a project member.</span>"; die();
	}

	$members = $model->memberships->getMembers();	//%	set of all project memberships [array]
	foreach($members as $membership) {					
		if ($userUID == $membership['userUID']) {
			$position = $membership['position'];	
			$isAdmin = $membership['admin'];
		}
	}

	$model->memberships->remove($userUID);

	//----------------------------------------------------------------------------------------------
	//	raise event
	//----------------------------------------------------------------------------------------------
	$args = array(
		'module' => 'projects',
		'projectUID' => $model->UID,
		'userUID' => $userUID,
		'role' => $role
	);

	$kapenta->raiseEvent('projects', 'member_removed', $args);
	echo "<span class='ajaxmsg'>Removed from project.</span>";

?>
