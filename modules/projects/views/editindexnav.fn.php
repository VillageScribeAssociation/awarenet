<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	nav box (iframe) for editing a project's index
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_editindexnav($args) {
		global $kapenta;
		global $user;

	$html = '';				//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//if ($user->authHas('projects', 'projects_project', 'show', 'TODO:UIDHERE') == false) { return false; }

	$html .= "<iframe name='editProjectIndex' id='editpi'"
		  . " src='%%serverPath%%projects/editindex/" . $model->alias . "'"
		  . " width='300' height='120' frameborder='no'></iframe>\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
