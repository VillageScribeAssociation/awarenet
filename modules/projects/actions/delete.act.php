<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a project (and all memberships, revisions, etc)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST))
		{ $page->do404('Projects not specified (UID).'); }
    
	$model = new Projects_Project($_POST['UID']);
	if (false == $user->authHas('projects', 'Projects_Project', 'delete', $model->UID))
		{ $page->do403('You are not authorzed to delete this project.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the project and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted project: " . $model->title);
	$page->do302('projects/');

?>
