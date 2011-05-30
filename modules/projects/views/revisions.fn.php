<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides UID [string]

function projects_revisions($args) {
	global $db;

	global $kapenta;

	require_once($kapenta->installPath . 'modules/projects/inc/diff.inc.php');

	if ($user->authHas('projects', 'projects_project', 'show', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['UID'] = $args['projectUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	load all revisions
	//----------------------------------------------------------------------------------------------

	$sql = "select * from projects_revision "
		 . "where refUID='" . $db->addMarkup($args['UID']) . "' order by editedOn";

	$result = $db->query($sql);
	$lastRow = array();
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		if (count($lastRow) > 0) {
			$revisionLink = '/projects/revision/' . $row['UID'];
			$revisionDate = date('Y-m-d', strtotime($row['editedOn']));

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