<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to create a new project (formatted for nav)
//--------------------------------------------------------------------------------------------------

function projects_newprojectform($args) {
	if (authHas('projects', 'edit', '') == false) { return false; }
	return loadBlock('modules/projects/views/newprojectform.block.php');
}

//--------------------------------------------------------------------------------------------------

?>

