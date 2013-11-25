<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for changing a project's status (open|closed|locked)
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Project_Project object [string]
//opt: projectUID - overrides raUID if present [string]

function projects_changestatusform($args) {
	global $user;
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(Project not specified)'; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return '(project not found)'; }

	if (false == $user->authHas('projects', 'projects_project', 'setstatus', $model->UID)) {
		return "<small>(You are not permitted to change this project's status.)</small>"; 
	}

	//----------------------------------------------------------------------------------------------
	//	make select box
	//----------------------------------------------------------------------------------------------
	$options = array('open', 'closed', 'locked');
	$select = "<select name='status'>\n";
	foreach($options as $option) {
		$selected = '';
		if ($option == $model->status) { $selected = " selected='selected'"; }
		$select .= "<option value='$option'$selected>$option</option>\n";
	}
	$select .= "</select>\n";

	//----------------------------------------------------------------------------------------------
	//	assemble the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/changestatusform.block.php');

	$labels = array(
		'UID' => $model->UID,
		'selectStatus' => $select
	);

	$html = $theme->replaceLabels($labels, $block);
	$html = $theme->ntb($html, 'Project Status', 'divProjectStatus', 'hide');

	return $html;
}


?>
