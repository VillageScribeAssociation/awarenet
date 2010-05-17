<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	nav box (iframe) for editing a project's membership
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_editmembersnav($args) {
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$html = "<iframe name='editProjectMembers' id='editpm'"
		  . " src='%%serverPath%%projects/editmembers/" . $args['raUID'] . "'"
		  . " width='300' height='120' frameborder='no'></iframe>\n";

	return $html;
}


?>

