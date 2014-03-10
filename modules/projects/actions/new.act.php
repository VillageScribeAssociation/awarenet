<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new project, set the title and and the originating user as a project admin
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('projects', 'projects_project', 'new')) 
		{ $kapenta->page->do403('you are not authorized to create new projects'); }

	if ('public' == $user->role) { $kapenta->page->do403('Only registered users can create projects.'); }

	//----------------------------------------------------------------------------------------------
	//	create project
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project();
	$model->title = 'New Project ' . $model->UID;

	if (true == array_key_exists('title', $_POST)) {
		$model->title = $utils->cleanTitle($_POST['title']);
		if ('' == $model->title)  {
			$session->msg("Please choose a name to create your project with.", 'bad');
			$kapenta->page->do302('projects/');
		}
	}

	$report = $model->save(); 
	if ('' == $report) {
		$session->msg('Created new project: ' . $model->title, 'ok');
	} else { 
		$session->msg('Could not create new project:<br/>' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	create membership for current user
	//----------------------------------------------------------------------------------------------
	$userNameBlock = '[[:users::namelink::userUID=' . $user->UID . ':]]';
	$check = $model->memberships->add($user->UID, 'admin');
	if (true == $check) { 
		$session->msg('Add project admin: ' . $userNameBlock, 'ok');
	} else {
		$session->msg('Could not add ' . $userNameBlock . ' as project admin.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect to edit page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('projects/edit/' . $model->alias);

?>
