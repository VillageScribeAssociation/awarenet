<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new project
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('projects', 'Projects_Project', 'new')) 
		{ $page->do403('you are not authorized to create new projects'); }

	if ('public' == $user->role) { $page->do403('Only registered users can create projects.'); }

	//----------------------------------------------------------------------------------------------
	//	create project
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project();
	$model->title = 'New Project ' . $projectUID;

	if (true == array_key_exists('title', $_POST)) {
		$title = $utils->cleanString($_POST['title']);
		if ('' == $title)  {
			$session->msg("Please choose a name to create your project with.", 'bad');
			$page->do302('projects/');
		}
		$model->title = $title;
		$model->save(); 

	} else { $model->save(); }

	//----------------------------------------------------------------------------------------------
	//	create membership for current user
	//----------------------------------------------------------------------------------------------
	$model->addMember($user->UID, 'admin')

	//----------------------------------------------------------------------------------------------
	//	redirect to edit page
	//----------------------------------------------------------------------------------------------
	$page->do302('projects/edit/' . $model->alias);

?>
