<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add project members
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]

function projects_addmemberform($args) {
		global $theme;
		global $kapenta;

	$html = '';		//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) {
		return '';	// no permission
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array('raUID' => $args['raUID']);	
	$block = $theme->loadBlock('modules/projects/views/addmemberform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
