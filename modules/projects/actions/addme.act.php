<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	current user is requesting to join a project
//--------------------------------------------------------------------------------------------------
	
	if ('public' == $user->role) { $page->do403(); }	// public and banned users can't do this	

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not given.'); }
	if ('askToJoin' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Project not specified (UID).'); }

	//----------------------------------------------------------------------------------------------
	//	check for an existing request
	//----------------------------------------------------------------------------------------------
	//$sql = "select * from Projects_Membership "
	//	 . "where projectUID='" . $db->addMarkup($_POST['UID']) . "' "
	//	 . "and userUID='" . $user->UID . "'";

	$model = new Projects_Membership();
	$model->findAndLoad($_POST['UID'], $user->UID);
	if (true == $model->loaded) { 
		$session->msg('You have already asked to join this project.', 'bad');
		$page->do302('projects/' . $_POST['UID']); 
	}

	//----------------------------------------------------------------------------------------------
	//	no existing request, make one 
	//----------------------------------------------------------------------------------------------
	$model->projectUID = $_POST['UID'];
	$model->userUID = $user->UID;
	$model->role = 'asked';
	$model->joined = $db->datetime();
	$report = $model->save();
					
	//----------------------------------------------------------------------------------------------
	//	notify admins of request, cc to user making it
	//----------------------------------------------------------------------------------------------

	$project = new Projects_Project($_POST['UID']);

	$url = "%%serverPath%%projects/" . $project->alias;
	$link = "<a href='" . $url . "'>" . $project->title . "</a>";

	$title = $user->getNameLink() . ' would like to join project: ' . $link;	
	$content = 'Request via form on the projectpage, no message attached.';

	if (true == array_key_exists('message', $_POST)) 
		{ $content = "message attached: " . stripslashes($utils->cleanString($_POST['message'])); }

	$nUID = $notifications->create('projects', 'Projects_Project', $project->UID, $title, $content, $url);

	$notifications->addProjectAdmins($nUID, $project->UID);
	$notifications->addUser($nUID, $user->UID);

	//----------------------------------------------------------------------------------------------
	//	return to project page
	//----------------------------------------------------------------------------------------------

	$msg = "You have requested to join this project. "
		 . "Please be patient in waiting for a response from the people "
		 . "in charge of this project.\n";

	$session->msg($msg, 'ok');
	$page->do302('projects/' . $project->alias);

?>
