<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/inc/diff.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides UID [string]

function projects_revisions($args) {
	global $kapenta;
	global $user;
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['UID'] = $args['projectUID']; }
	if (false == array_key_exists('UID', $args)) { return false; }

	$model = new Projects_Project($args['UID']);
	if (false == $model->loaded) { return 'Project not found.'; }

	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { 
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//	load all revisions
	//----------------------------------------------------------------------------------------------

	$sql = "select * from projects_revision "
		 . "where refUID='" . $kapenta->db->addMarkup($args['UID']) . "' order by editedOn";

	$result = $kapenta->db->query($sql);
	$lastRow = array();
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);

		if (count($lastRow) > 0) {
			$revisionLink = '/projects/revision/' . $row['UID'];
			$revisionDate = date('Y-m-d', $kapenta->strtotime($row['editedOn']));

			$item = "<hr/>\n"
				  . "<table noborder>\n"
				  . "  <tr>\n"
				  . "    <td valign='top' width='200'>"
				  . "[[:users::summarynav::userUID=" . $row['editedBy'] . ":]]<br/>"
				  . "<a href='/projects/revision/" . $row['UID'] . "'>[view]</a>"
			      . "    </td>"
				  . "    <td valign='top'>"; 

			$ops = diffHtml($row['content'], $lastRow['content']);
			foreach($ops as $op) { $item .= diffOpToHtml($op); }

			$item .= "<br/><b>Saved:</b> " . $row['editedOn'] . "<br/>\n"
				  . "    </td>"
				  . "  </tr>"
				  . "</table>";

			$html = $item . $html;

		}

		$lastRow = $row;
	}

	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
