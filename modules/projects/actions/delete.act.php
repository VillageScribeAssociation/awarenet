<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a project (and all memberships, revisions, etc)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST))
		{ $kapenta->page->do404('Projects not specified (UID).'); }
    
	$model = new Projects_Project($_POST['UID']);
	if (false == $user->authHas('projects', 'projects_project', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorzed to delete this project.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the project and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted project: " . $model->title);
	$kapenta->page->do302('projects/');

?>
