<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	nav box (iframe) for editing a project's membership
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_editmembersnav($args) {
	global $user;
	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = "<iframe name='editProjectMembers' id='editpm'"
		  . " src='%%serverPath%%projects/editmembers/" . $model->UID . "'"
		  . " width='300' height='120' frameborder='no'></iframe>\n";

	return $html;
}


?>

