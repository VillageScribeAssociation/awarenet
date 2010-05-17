<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list requests made by others to join a project
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_requestsjoinnav($args) {
	global $user;
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	discover if user is an admin of this project, if not, do nothing
	//----------------------------------------------------------------------------------------------
	$sql = "select * from projectmembers "
		 . "where projectUID='" . sqlMarkup($args['raUID']) . "' "
		 . "and userUID='" . $user->data['UID'] . "' and role='admin'";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		//------------------------------------------------------------------------------------------
		//	user is a project admin
		//------------------------------------------------------------------------------------------
		
		$sql = "select * from projectmembers "
			 . "where projectUID='" . sqlMarkup($args['raUID']) . "' and role='asked'";

		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) {		
			//--------------------------------------------------------------------------------------
			//	people have asked to join this project
			//--------------------------------------------------------------------------------------
			$html .= "[[:theme::navtitlebox::label=Have Asked To Join:]]\n";
			while ($row = dbFetchAssoc($result)) {

				$addUrl = "%%serverPath%%projects/acceptmember/" . $row['UID'];

				$html .= "[[:users::summarynav::userUID=" . $row['userUID'] . ":]]\n"
					   . "<a href='$addUrl'>[add as project member >> ]</a><hr/>\n";

			}
		}
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

