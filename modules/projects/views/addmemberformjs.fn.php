<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add project members
//--------------------------------------------------------------------------------------------------
//arg: projectUID - overrides raUID [string]
//arg: userUID - UID of a Users_User object [string]

function projects_addmemberformjs($args) {
	global $theme;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('projectUID', $args)) 
		{ return "<span class='ajaxerror'>Missing project UID.</span>"; }

	if (false == array_key_exists('userUID', $args)) 
		{ return "<span class='ajaxerror'>Missing user UID.</span>"; }

	$model = new Projects_Project($args['projectUID']);
	if (false == $model->loaded) { return "<span class='ajaxerror'>Unkown project.</span>"; }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) {
		return '';
	}

	if ($model->memberships->hasMember($args['userUID'])) { 
		return "<span class='ajaxwarn'>This person is already a member.</span>"; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/addmemberformjs.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
