<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all projects which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]

function projects_listuserprojectsnav($args) {
	global $user;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and auth
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { return '[[:users::pleaselogin:]]'; }
	if (array_key_exists('userUID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	get list projects by checking memberships
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "userUID='" . sqlMarkup($args['userUID']) . "'";	// this user
	$conditions[] = "(role='admin' OR role='member')";					// only confirmed

	$range = dbLoadRange('projectmembers', '*', $conditions, 'joined');

	foreach($range as $row) 
		{ $html .= "[[:projects::summarynav::projectUID=" . $row['projectUID'] . ":]]"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

