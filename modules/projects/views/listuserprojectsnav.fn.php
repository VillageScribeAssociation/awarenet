<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all projects which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]

function projects_listuserprojectsnav($args) {
	global $db, $user;
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
	$conditions[] = "userUID='" . $db->addMarkup($args['userUID']) . "'";	// this user
	$conditions[] = "(role='admin' OR role='member')";						// only confirmed

	$range = $db->loadRange('Projects_Membership', '*', $conditions, 'joined');

	foreach($range as $row) 
		{ $html .= "[[:projects::summarynav::projectUID=" . $row['projectUID'] . ":]]\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

