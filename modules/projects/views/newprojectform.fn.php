<?

//--------------------------------------------------------------------------------------------------
//|	form to create a new project (formatted for nav)
//--------------------------------------------------------------------------------------------------

function projects_newprojectform($args) {
	global $theme, $user;
	if (false == $user->authHas('projects', 'projects_project', 'new')) { return ''; }
	$block = $theme->loadBlock('modules/projects/views/newprojectform.block.php');
	return $block;
}

//--------------------------------------------------------------------------------------------------

?>

