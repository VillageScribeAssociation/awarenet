<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	ask to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_askjoinnav($args) {
	global $user;
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	determine if user is a member of project already
	//----------------------------------------------------------------------------------------------
	$sql = "select * from projectmembers "
		 . "where projectUID='" . sqlMarkup($args['raUID']) . "' "
		 . "and userUID='" . $user->data['UID'] . "'";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		$row = dbFetchAssoc($result);
		if ($row['role'] == 'asked') {
			$html = "[[:theme::navtitlebox::label=Ask to Join Project:]]\n"
				  . "You have already asked to join this project.<br/><br/>";
		}
	} else {
		$ary = array(	'userUID' => $user->data['UID'], 
						'userName' => $user->getName(),
						'projectUID' => $args['raUID']
					);
		$html = replaceLabels($ary, loadBlock('modules/projects/views/addme.block.php'));
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

