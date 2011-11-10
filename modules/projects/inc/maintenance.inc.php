<?
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	maintain the projects module
//--------------------------------------------------------------------------------------------------

function projects_maintenance() {
	global $db, $user, $theme, $aliases;
	$report = '';		//%	return value [string]

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	//----------------------------------------------------------------------------------------------
	//	check projects
	//----------------------------------------------------------------------------------------------

	$report .= "<h2>Checking project memberships...</h2>";

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from projects_project";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//------------------------------------------------------------------------------------------
		//	check projects
		//------------------------------------------------------------------------------------------
		$set = $aliases->getAll('projects', 'projects_project', $row['UID']);
		if (0 == count($set)) {
			$model = new Projects_Project($row['UID']);
			if (true == $model->loaded) { $model->save(); }

			$errors[] = array($row['UID'], $row['title'], 'Added alias.');

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

	//----------------------------------------------------------------------------------------------
	//	check memberships
	//----------------------------------------------------------------------------------------------

	$report .= "<h2>Checking project memberships...</h2>";

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from projects_membership";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//-----------------------------------------------------------------------------------------
		//	check reference to project
		//-----------------------------------------------------------------------------------------
		if (false == $db->objectExists('projects_project', $row['projectUID'])) {
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
		if (false == $db->objectExists('users_user', $row['userUID'])) {
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
