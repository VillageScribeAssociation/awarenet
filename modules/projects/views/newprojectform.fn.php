<?

//--------------------------------------------------------------------------------------------------
//|	form to create a new project (formatted for nav)
//--------------------------------------------------------------------------------------------------

function projects_newprojectform($args) {
		global $theme;
		global $user;

	if (false == $user->authHas('projects', 'projects_project', 'new')) { return ''; }
	$block = $theme->loadBlock('modules/projects/views/newprojectform.block.php');
	$block = $theme->ntb($block, 'Create New Project', 'divNewProjectForm', 'hide');
	return $block;
}

//--------------------------------------------------------------------------------------------------

?>

