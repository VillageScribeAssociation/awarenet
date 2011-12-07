<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	change project status
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'changeStatus' [string]
//postarg: UID - UID of a Projects_Project object [string]
//postarg: status - new status for the project [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not given.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Project UID not given.'); }
	if (false == array_key_exists('status', $_POST)) { $page->do404('New status not given.'); }

	if ('changeStatus' != $_POST['action']) { $page->do404('Action not recognized.'); }

	if (
		('open' != $_POST['status']) &&
		('closed' != $_POST['status']) &&
		('locked' != $_POST['status'])
	) { $page->do403('Invalid status.'); }

	$model = new Projects_Project($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Project not found.'); }

	if (false == $user->authHas('projects', 'projects_project', 'setstatus', $model->UID)) {
		$page->do403("You are not permitted to change this project's status."); 
	}

	//----------------------------------------------------------------------------------------------
	//	checge the status
	//----------------------------------------------------------------------------------------------
	$model->status = $_POST['status'];	
	$report = $model->save();
	if ('' == $report) { $session->msg("Project status set to: " . $model->status, 'ok'); }
	else { $session->msg("Could not set project status:<br/>" . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to the project otions page
	//----------------------------------------------------------------------------------------------
	$page->do302('projects/editabstract/' . $model->alias);	

?>
