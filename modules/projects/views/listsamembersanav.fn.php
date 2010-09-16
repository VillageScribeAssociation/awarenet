<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list projects with the same members as this one
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a project [string]
//opt: projectUID - overrides UID [string]
//opt: num - maximum number of projects to show, default is 10 [string]

function projects_listsamembersanav($args) {
	global $db;

	global $user;
	$limit = 10;
	$html = '';
	$projects = array();

	//----------------------------------------------------------------------------------------------
	//	check arguments and auth
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('projectUID', $args)) { $args['UID'] = $args['projectUID'];}	
	if (false == array_key_exists('UID', $args)) { return '';}	
	//if (false == $user->authHas('projects', 'Projects_Project', 'show', $args['UID'])){return '';}
	if (true == array_key_exists('limit', $args)) { $limit = (int)$args['limit']; }
	if (false == $db->objectExists('Projects_Project', $args['UID'])) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	get all members of this project
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "projectUID='" . $db->addMarkup($args['UID']) . "'"; // members of this project
	$conditions[] = "(role='admin' OR role='member')";				// only those who are confirmed
	$membersRange = $db->loadRange('Projects_Membership', '*', $conditions, '', '', '');

	//----------------------------------------------------------------------------------------------
	//	get all projects belonging to these members, and count overlap of members
	//----------------------------------------------------------------------------------------------

	foreach($membersRange as $member) {
		$conditions = array();
		$conditions[] = "userUID='" . $member['userUID'] . "'";				// same member
		$conditions[] = "projectUID!='" . $db->addMarkup($args['UID']) . "'";// but not this project
		$conditions[] = "(role='admin' OR role='member')";					// only confirmed
		$projectsRange = $db->loadRange('Projects_Membership', '*', $conditions, '', '', '');

		//------------------------------------------------------------------------------------------
		//	collect project UIDs in an array, counting the number of times they show up
		//------------------------------------------------------------------------------------------
		foreach($projectsRange as $row) {
			if (true == array_key_exists($row['projectUID'], $projects)) {
				$projects[$row['projectUID']] += 1;
			} else {
				$projects[$row['projectUID']] = 1;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	sort according to memebrship overlap (lots of common members -> top of list)
	//----------------------------------------------------------------------------------------------
	arsort($projects);

	foreach($projects as $projectUID => $count) {
		$limit--;
		if ((0 <= $limit) && ($projectUID != $args['UID'])) {
			$html .= "[[:projects::summarynav::projectUID=" . $projectUID . ":]]\n";
			//$html .= "<small>score: $count</small><br/>\n";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
