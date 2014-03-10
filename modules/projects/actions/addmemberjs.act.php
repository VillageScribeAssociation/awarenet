<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a member to a project, return HTML snippet to browser JS client
//--------------------------------------------------------------------------------------------------
//+	a projectUID and userUID and role should be posted along with 'addMember as the form action

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) 
		{ echo "<span class='ajaxerror'>Action not given.</span>"; die(); }

	if ('addMember' != $_POST['action']) 
		{ echo "<span class='ajaxerror'>Action not recognized.</span>"; die(); }

	if (false == array_key_exists('userUID', $_POST)) 
		{ echo "<span class='ajaxerror'>User UID not given.</span>"; die(); }

	if (false == array_key_exists('projectUID', $_POST)) 
		{ echo "<span class='ajaxerror'>Project UID not given.</span>"; die(); }

	if (false == array_key_exists('role', $_POST)) 
		{ echo "<span class='ajaxerror'>User role not given.</span>"; die(); }

	$userUID = $_POST['userUID'];
	$projectUID = $_POST['projectUID'];
	$xrole = $utils->cleanTitle($_POST['role']);		// $role is already global object

	if (false == $kapenta->db->objectExists('users_user', $userUID)) 
		{ echo "<span class='ajaxerror'>User not recognized.</span>"; die(); }

	$model = new Projects_Project($_POST['projectUID']);
	if (false == $model->loaded) { echo "<span class='ajaxerror'>Unknown project.</span>"; die(); }

	if (false == $user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) {
		echo "<span class='ajaxerror'>Not authorized.</span>"; die(); 
	}

	//----------------------------------------------------------------------------------------------
	//	add the member, raise event and we're done
	//----------------------------------------------------------------------------------------------
	$model->memberships->add($userUID, $xrole);		//	this also updates school index

	$args = array(
		'module' => 'projects',
		'projectUID' => $model->UID,
		'userUID' => $userUID,
		'role' => $xrole
	);

	$kapenta->raiseEvent('projects', 'member_added', $args);
	echo "<span class='ajaxmsg'>Added to project.</span>";

?>
