<?

//--------------------------------------------------------------------------------------------------
//	save some change to a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and load model
	//----------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/projects/models/projects.mod.php');

	$authorised = false;
	$model = new Project();

	if ( (array_key_exists('UID', $_POST) == true) 
		AND (dbRecordExists('projects', $_POST['UID']) == true) ) {

		$model->load($_POST['UID']);
		if ($model->isMember($user->data['UID']) == true) { $authorised = true; }
	
	} else { do404(); } // no such project, or UID not specified

	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }
	if ($authorised == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	save changes to title
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	    AND ($_POST['action'] == 'saveChangeTitle')
		AND (array_key_exists('title', $_POST) == true) ) {

		//------------------------------------------------------------------------------------------
		//	check that it's different, make revision
		//------------------------------------------------------------------------------------------
		if ($model->data['title'] != clean_string($_POST['title'])) { $model->saveRevision(); }
		
		//------------------------------------------------------------------------------------------
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->data['title'] = clean_string($_POST['title']);
		$model->save();
		
		do302('projects/edit/' . $model->data['recordAlias']);
	}


	//----------------------------------------------------------------------------------------------
	//	save changes to abstract
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	    AND ($_POST['action'] == 'saveAbstract')
		AND (array_key_exists('abstract', $_POST) == true) ) {

		//------------------------------------------------------------------------------------------	
		//	note the revision
		//------------------------------------------------------------------------------------------
		if ($model->data['abstract'] != $_POST['abstract']) { $model->saveRevision(); }

		//------------------------------------------------------------------------------------------	
		//	save the record
		//------------------------------------------------------------------------------------------
		$model->data['abstract'] = $_POST['abstract'];
		$model->save();
		
		do302('projects/edit/' . $model->data['recordAlias']);
	}

	//----------------------------------------------------------------------------------------------
	//	add a new section
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	    AND ($_POST['action'] == 'addSection')
		AND (array_key_exists('sectionTitle', $_POST) == true) ) {

		$sectionTitle = clean_string($_POST['sectionTitle']);
		$weight = $model->getMaxWeight() + 1;
		$model->addSection($sectionTitle, $weight);
		$_SESSION['sMessage'] .= "Added new section $sectionTitle<br/>\n";
		$model->saveRevision();
		do302('projects/editindex/' . $model->data['recordAlias']);
	}

	//----------------------------------------------------------------------------------------------
	//	save changes to a section
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	    AND ($_POST['action'] == 'saveSection')
		AND (array_key_exists('sectionUID', $_POST) == true) 
		AND (array_key_exists($_POST['sectionUID'], $model->sections) == true) ) {

		$sectionTitle = clean_string($_POST['sectionTitle']);
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
		if ($newVersion != $oldVersion) { $model->saveRevision(); }
		
		do302('projects/editsection/section_' . $_POST['sectionUID'] .  '/' . $model->data['recordAlias']);
	}

	//----------------------------------------------------------------------------------------------
	//	nothing doing, unsupported action requested?
	//----------------------------------------------------------------------------------------------
	
	do404();

?>
