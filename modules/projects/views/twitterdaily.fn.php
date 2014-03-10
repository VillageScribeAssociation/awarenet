<?

//--------------------------------------------------------------------------------------------------
//*	summarize daily project activity for twitter microreport
//--------------------------------------------------------------------------------------------------
//opt: date - date to be shown, default is today, YYYY-MM-DD [string]

function projects_twitterdaily($args) {
	global $kapenta;
	global $kapenta;

	$date = substr($kapenta->datetime(), 0, 10);
	$txt = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('date', $args)) { $date = $args['date']; }

	//----------------------------------------------------------------------------------------------
	//	count new objects
	//----------------------------------------------------------------------------------------------
	$conditions = array("DATE(createdOn) = '" . $kapenta->db->addMarkup($date) . "'");
	$newProjects = $kapenta->db->countRange('projects_project', $conditions);
	$newRevisions = $kapenta->db->countRange('projects_change', $conditions);
	$newMembers = $kapenta->db->countRange('projects_membership', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the snippet
	//----------------------------------------------------------------------------------------------
	if ($newProjects > 0) { $txt .= " Projects: " . $newProjects; }
	if ($newRevisions > 0) { $txt .= " Project revisions: " . $newRevisions; }
	if ($newMembers > 0) { $txt .= " Project members: " . $newMembers; }

	return $txt;
}

?>
