<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	remove (hide) a section of a project
//--------------------------------------------------------------------------------------------------
//+	note that project sections are not actually deleted, but hidden so that they can be undeleted 
//+	from Projects_Changes.

	//----------------------------------------------------------------------------------------------
	//	check POST variables and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteSection' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Section UID not specified'); }

	//----------------------------------------------------------------------------------------------
	//	load the project and check that user has edit permissions on this project
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Section not found.'); }
	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $model->projectUID))
		{ $kapenta->page->do403('You are not authozed to edit this project'); }

	//if (false == $model->hasEditAuth($kapenta->user->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	hide the section and redirect back to edit page
	//----------------------------------------------------------------------------------------------
	$model->hidden = 'yes';
	$report = $model->save();

	//TODO: revisions and notifications here

	//----------------------------------------------------------------------------------------------
	//	delete the section and redirect back to abstract
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg("Removed section: '" . $model->title . "'", 'ok'); } 
	else { $kapenta->session->msg('Section not removed:<br/>' . $report, 'bad'); }
		
	$kapenta->page->do302('projects/editsection/' . $model->UID);

?>
