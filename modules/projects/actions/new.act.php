<?

//--------------------------------------------------------------------------------------------------
//	add a new project
//--------------------------------------------------------------------------------------------------

	if (authHas('projects', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/projects/models/projects.mod.php');

	//----------------------------------------------------------------------------------------------
	//	create project
	//----------------------------------------------------------------------------------------------

	$projectUID = createUID();
	$model = new Project();
	$model->data['UID'] = $projectUID;
	$model->data['title'] = 'New Project ' . $projectUID;

	if (array_key_exists('title', $_POST) == true) {
		$title = clean_string($_POST['title']);
		if ($title == '')  {
			$_SESSION['sMessage'] .= "Please choose a name to create your project with.<br/>\n";
			do302('/projects/');
		}
		$model->data['title'] = $title;
		$model->save(); 

	} else { $model->save(); }

	//----------------------------------------------------------------------------------------------
	//	create membership for current user
	//----------------------------------------------------------------------------------------------
	
	$model = new ProjectMembership();
	$model->data['UID'] = createUID();
	$model->data['projectUID'] = $projectUID;
	$model->data['userUID'] = $user->data['UID'];
	$model->data['role'] = 'admin';
	$model->data['joined'] = mysql_datetime();
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect ro edit page
	//----------------------------------------------------------------------------------------------

	do302('projects/edit/' . $projectUID);

?>
