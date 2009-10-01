<?

//--------------------------------------------------------------------------------------------------
//	current user is requesting to join a project
//--------------------------------------------------------------------------------------------------
	
	if ($user->data['ofGroup'] == 'public') { do403(); }	// public and banned users can't do this
	
	if ( (array_key_exists('action', $_POST) == true)
	   && ($_POST['action'] == 'askToJoin')
	   && (array_key_exists('UID', $_POST) == true)
	   && (dbRecordExists('projects', $_POST['UID']) == true) ) {

		require_once($installPath . 'modules/projects/models/projects.mod.php');

		//------------------------------------------------------------------------------------------
		//	check for an existing request
		//------------------------------------------------------------------------------------------

		$sql = "select * from projectmembers "
			 . "where projectUID='" . sqlMarkup($_POST['UID']) . "' "
			 . "and userUID='" . $user->data['UID'] . "'";

		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { do302('projects/' . $_POST['UID']); }

		//------------------------------------------------------------------------------------------
		//	no existing request, make one 
		//------------------------------------------------------------------------------------------

		$fields = array('UID' => createUID(), 'projectUID' => sqlMarkup($_POST['UID']),
						'userUID' => $user->data['UID'], 'datetime' => mysql_datetime() );
						
		$sql = "insert into projectmembers values "
			 . "('%%UID%%', '%%projectUID%%', '%%userUID%%', 'asked', '%%datetime%%')";

		dbQuery(replaceLabels($fields, $sql));

		//------------------------------------------------------------------------------------------
		//	notify admins of request
		//------------------------------------------------------------------------------------------

		$model = new Project($_POST['UID']);

		$pUID = $_POST['UID'];													// UID of project
		$nUID = createUID();													// notice UID
		$uUID = $user->data['UID'];												// user UID
		$fromUrl = "%%serverPath%%users/profile/" . $user->data['recordAlias'];	// user profile
		$projectUrl = "%%serverPath%%projects/" . $model->data['recordAlias'];	
		$projectLink = "<a href='" . $projectUrl . "'>" . $model->data['title'] . "</a>";
		$title = $user->getNameLink() . ' would like to join your project: ' . $projectLink;

		$message = "(no message attached)";
		if (array_key_exists('message', $_POST) == true) { 
			$message = "message attached: " . stripslashes(clean_string($_POST['message']));
		}

		notifyProjectAdmins($pUID, $nUID, $uUID, $fromUrl, $title, $message, $projectUrl, '');

		//------------------------------------------------------------------------------------------
		//	return to project page
		//------------------------------------------------------------------------------------------

		$_SESSION['sMessage'] .= "[[:theme::navtitlebox::width=570::label=Request Made:]]\n"
							   . "You have requested to join this project. "
							   . "Please be patient in waiting for a response from the people "
							   . "in charge of this project.<br/><br/>\n";

		do302('projects/' . $_POST['UID']);

	} else {
		do404();
	}

?>
