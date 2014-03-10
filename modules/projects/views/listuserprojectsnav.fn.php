<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all projects which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]

function projects_listuserprojectsnav($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';				//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and auth
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (array_key_exists('userUID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	get list projects by checking memberships
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "userUID='" . $kapenta->db->addMarkup($args['userUID']) . "'";	// this user
	$conditions[] = "(role='admin' OR role='member')";						// only confirmed

	$range = $kapenta->db->loadRange('projects_membership', '*', $conditions, 'joined');

	if (0 == count($range)) { return ''; }

	foreach($range as $row) 
		{ $html .= "[[:projects::summarynav::projectUID=" . $row['projectUID'] . ":]]\n"; }

	$html = $theme->ntb($html, 'Projects', 'divUserProjects', 'show');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

