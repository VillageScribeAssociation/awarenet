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
		AND (true == $db->objectExists('Projects_Project', $_POST['UID'])) ) {

		$model->load($_POST['UID']);
		if (true == $model->isMember($user->UID)) { $authorised = true; }
	
	} else { $page->do404(); } // no such project, or UID not specified

	if ('admin' == $user->role) { $authorised = true; }
	if (false == $authorised) { $page->do403(); }

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
		
		$page->do302('projects/edit/' . $model->alias);
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
			if ('' == $report) { $session->msg('Saved revision.', 'ok'); }
			else { $session->msg('Revision not saved.', 'bad'); }
		}

		//------------------------------------------------------------------------------------------	
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->abstract = $_POST['abstract'];
		$report = $model->save();
		if ('' == $report) { $session->msg('Saved changes to abstract.', 'ok'); }
		else { $session->msg('Could not save changes to abstract.', 'bad'); }		
		
		$page->do302('projects/edit/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	add a new section
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('action', $_POST))
	    AND ('addSection' == $_POST['action'])
		AND (true == array_key_exists('sectionTitle', $_POST)) ) {

		$sectionTitle = $utils->cleanString($_POST['sectionTitle']);
		$weight = $model->getMaxWeight() + 1;
		$model->addSection($sectionTitle, $weight);
		$_SESSION['sMessage'] .= "Added new section $sectionTitle<br/>\n";

		$report = $model->saveRevision();
		if ('' == $report) { $session->msg('Saved revision.', 'ok'); }
		else { $session->msg('Revision not saved.', 'bad'); }

		$page->do302('projects/editindex/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	save changes to a section
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('action', $_POST))
	    AND ('saveSection' == $_POST['action'])
		AND (true == array_key_exists('sectionUID', $_POST)) 
		AND (true == array_key_exists($_POST['sectionUID'], $model->sections)) ) {

		$sectionTitle = $utils->cleanString($_POST['sectionTitle']);
		$content = strip_tags($_POST['content'], $model->allowTags);

		//------------------------------------------------------------------------------------------
		//	check for changes (revision)
		//------------------------------------------------------------------------------------------
		$oldVersion = $model->getSimpleHtml();
		
		//------------------------------------------------------------------------------------------
		//	save the changes
		//------------------------------------------------------------------------------------------
		$model->sections[$_POST['sectionUID']]['title'] = strip_tags($sectionTitle);
		$model->sections[$_POST['sectionUID']]['content'] = $content;
		$model->save();

		//------------------------------------------------------------------------------------------
		//	save revision (if changed)
		//------------------------------------------------------------------------------------------
		$newVersion = $model->getSimpleHtml();
		if ($newVersion != $oldVersion) {
			$report = $model->saveRevision();
			if ('' == $report) { $session->msg('Saved revision.', 'ok'); }
			else { $session->msg('Revision not saved.', 'bad'); }
		}
		
		$page->do302('projects/editsection/section_' . $_POST['sectionUID'] .  '/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	nothing doing, unsupported action requested?
	//----------------------------------------------------------------------------------------------
	
	$page->do404();

?>
