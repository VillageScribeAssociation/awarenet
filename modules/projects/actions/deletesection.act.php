<?

//--------------------------------------------------------------------------------------------------
//	delete a section of a project
//--------------------------------------------------------------------------------------------------

	if (authHas('projects', 'edit', '') == false) { do403(); }

	if ( (array_key_exists('action', $_POST)) 
	  AND ($_POST['action'] == 'deleteSection') 
	  AND (true == array_key_exists('UID', $_POST)) 
	  AND (true == array_key_exists('section', $_POST))
	  AND (true == dbRecordExists('projects', $_POST['UID'])) ) {

		//------------------------------------------------------------------------------------------
		//	load the project and check that user has edit permissions on this project
		//------------------------------------------------------------------------------------------
	  
		require_once($installPath . 'modules/projects/models/project.mod.php');
	  
		$model = new Project();
		$model->load($_POST['UID']);
		if (false == $model->hasEditAuth($user->data['UID'])) { do403(); }

		//------------------------------------------------------------------------------------------
		//	delete the section
		//------------------------------------------------------------------------------------------

		$sectionTitle = $model->sections[$_POST['section']]['title'];		
		$result = $model->deleteSection($_POST['section']);

		if (true == $result) { $_SESSION['sMessage'] .= "Deleted section: '" . $sectionTitle . "'"; } 
		else { $_SESSION['sMessage'] .= "Section not found"; }
		
		do302('projects/editabstract/' . $model->data['recordAlias']);
	  
	} else { do404(); }

?>
