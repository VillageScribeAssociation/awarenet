<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a section of a project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST variables and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteSection' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Project not specified (UID)'); }
	if (false == array_key_exists('section', $_POST)) { $page->do404('Section not specified.'); }

	//----------------------------------------------------------------------------------------------
	//	load the project and check that user has edit permissions on this project
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($_POST['UID']);
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID))
		{ $page->do403('You are not authozed to edit this project'); }

	//if (false == $model->hasEditAuth($user->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete the section and redirect back to abstract
	//----------------------------------------------------------------------------------------------
	$sectionTitle = $model->sections[$_POST['section']]['title'];		
	$result = $model->deleteSection($_POST['section']);

	if (true == $result) { $_SESSION['sMessage'] .= "Deleted section: '" . $sectionTitle . "'"; } 
	else { $_SESSION['sMessage'] .= "Section not found"; }
		
	$page->do302('projects/editabstract/' . $model->alias);

?>
