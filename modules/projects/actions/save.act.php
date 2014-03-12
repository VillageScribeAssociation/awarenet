<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save some change to a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and load model
	//----------------------------------------------------------------------------------------------
	$authorised = false;
	$model = new Projects_Project();

	if ( (true == array_key_exists('UID', $_POST)) 
		AND (true == $kapenta->db->objectExists('projects_project', $_POST['UID'])) ) {

		$model->load($_POST['UID']);
		if (true == $model->hasMember($kapenta->user->UID)) { $authorised = true; }
	
	} else { $kapenta->page->do404(); } // no such project, or UID not specified

	if ('admin' == $kapenta->user->role) { $authorised = true; }
	if (false == $authorised) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	save changes to title
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('action', $_POST))
	    AND ('saveChangeTitle' == $_POST['action'])
		AND (true == array_key_exists('title', $_POST)) ) {

		//------------------------------------------------------------------------------------------
		//	check that it's different, make revision
		//------------------------------------------------------------------------------------------
		if ($model->title != $utils->cleanString($_POST['title'])) { $model->saveRevision(); }
		
		//------------------------------------------------------------------------------------------
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->title = $utils->cleanString($_POST['title']);
		$model->save();
		
		$kapenta->page->do302('projects/edit/' . $model->alias);
	}


	//----------------------------------------------------------------------------------------------
	//	save changes to abstract
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('action', $_POST))
	    AND ('saveAbstract' == $_POST['action'])
		AND (true == array_key_exists('abstract', $_POST)) ) {

		//------------------------------------------------------------------------------------------	
		//	note the revision
		//------------------------------------------------------------------------------------------
		if ($model->abstract != $_POST['abstract']) { 
			$report = $model->saveRevision();
			if ('' == $report) { $kapenta->session->msg('Saved revision.', 'ok'); }
			else { $kapenta->session->msg('Revision not saved.', 'bad'); }
		}

		//------------------------------------------------------------------------------------------	
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->abstract = $_POST['abstract'];
		$report = $model->save();
		if ('' == $report) { 
			//--------------------------------------------------------------------------------------
			//	notify project members and user's friends
			//--------------------------------------------------------------------------------------
			$ext = $model->extArray();
			$title = "Project update: " . $ext['nameLink'];
			$content = "" 
				. "[[:users::namelink::userUID=" . $kapenta->user->UID . ":]] "
				. "has made changes to the project abstract.";

			$nUID = $notifications->create(
				'projects', 'projects_project', $model->UID, 'projects_edit', 
				$title, $content, $ext['viewUrl']
			);

			$notifications->addProject($nUID, $model->UID);
			$notifications->addFriends($nUID, $kapenta->user->UID);
			$notifications->addAdmins($nUID);

			//--------------------------------------------------------------------------------------
			//	raise a microbog event for this
			//--------------------------------------------------------------------------------------
			$args = array(
				'refModule' => 'projects',
				'refModel' => 'projects_project',
				'refUID' => $model->UID,
				'message' => '#'. $kapenta->websiteName .' project updated - '. $model->title
			);

			$kapenta->raiseEvent('*', 'microblog_event', $args);

			$kapenta->session->msg('Saved changes to abstract.', 'ok'); 
		} else {
			$kapenta->session->msg('Could not save changes to abstract.', 'bad');
		}		
		
		$kapenta->page->do302('projects/edit/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	nothing doing, unsupported action requested?
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do404();

?>
