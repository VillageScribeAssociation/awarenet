<?
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the projects module
//-------------------------------------------------------------------------------------------------

function projects_maintenance() {
	global $db, $user, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking project memberships...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check image data
	//---------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from Projects_Membership";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//-----------------------------------------------------------------------------------------
		//	check reference to project
		//-----------------------------------------------------------------------------------------
		if (false == $db->objectExists('Projects_Project', $row['projectUID'])) {
			$report .= "project not found - " . $row['projectUID'] 
					. ", removed membership " . $row['UID'] . ".<br/>\n";

			$model = new Projects_Membership();
			$model->loadArray($row);
			$model->delete();			

			$errorCount++;
			$fixCount++;
		}

		//-----------------------------------------------------------------------------------------
		//	check reference to user
		//-----------------------------------------------------------------------------------------
		if (false == $db->objectExists('Users_User', $row['userUID'])) {
			$report .= "user not found - " . $row['userUID'] . ", removed this membership.<br/>\n";

			//$model = new Projects_Membership();
			//$model->loadArray($row);
			//$model->delete();			

			$errorCount++;
			$fixCount++;
		}

		$recordCount++;
	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
