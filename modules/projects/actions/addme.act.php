<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	current user is requesting to join a project
//--------------------------------------------------------------------------------------------------
	
	if ('public' == $user->role) { $page->do403(); }	// public and banned users can't do this	

	if ( (true == array_key_exists('action', $_POST))
	   && ('askToJoin' == $_POST['action'])
	   && (true == array_key_exists('UID', $_POST))
	   && (true == $db->objectExists('Projects_Project', $_POST['UID'])) ) {

		require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

		//------------------------------------------------------------------------------------------
		//	check for an existing request
		//------------------------------------------------------------------------------------------
		//$sql = "select * from Projects_Membership "
		//	 . "where projectUID='" . $db->addMarkup($_POST['UID']) . "' "
		//	 . "and userUID='" . $user->UID . "'";

		$model = new Projects_Membership();
		$model->findAndLoad($_POST['UID'], $user->UID);
		if (true == $model->loaded) { 
			$session->msg('You have already asked to join this project.', 'bad');
			$page->do302('projects/' . $_POST['UID']); 
		}

		//------------------------------------------------------------------------------------------
		//	no existing request, make one 
		//------------------------------------------------------------------------------------------

		$model->projectUID = $_POST['UID'];
		$model->userUID = $user->UID;
		$model->joined = $db->datetime();
		$report = $model = save();
					
		//------------------------------------------------------------------------------------------
		//	notify admins of request
		//------------------------------------------------------------------------------------------

		$model = new Projects_Project($_POST['UID']);

		$pUID = $_POST['UID'];													// UID of project
		$nUID = $kapenta->createUID();											// notice UID
		$uUID = $user->UID;														// user UID
		$fromUrl = "%%serverPath%%users/profile/" . $user->alias;				// user profile
		$projectUrl = "%%serverPath%%projects/" . $model->alias;	
		$projectLink = "<a href='" . $projectUrl . "'>" . $model->title . "</a>";
		$title = $user->getNameLink() . ' would like to join your project: ' . $projectLink;

		$message = "(no message attached)";
		if (array_key_exists('message', $_POST) == true) { 
			$message = "message attached: " . stripslashes($utils->cleanString($_POST['message']));
		}
		notifyProjectAdmins($pUID, $nUID, $uUID, $fromUrl, $title, $message, $projectUrl, '');

		//------------------------------------------------------------------------------------------
		//	return to project page
		//------------------------------------------------------------------------------------------

		$msg . "You have requested to join this project. "
			 . "Please be patient in waiting for a response from the people "
			 . "in charge of this project.\n";

		$session->msg($msg, 'ok');
		$page->do302('projects/' . $_POST['UID']);

	} else {
		$page->do404();
	}

?>
