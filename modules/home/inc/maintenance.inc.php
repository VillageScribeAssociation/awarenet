<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the gallery table
//-------------------------------------------------------------------------------------------------

function home_maintenance() {
	global $db, $theme;
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking Home_Static table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check image count of all gallery tables
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "select * from Home_Static";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		//TODO: check alias

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
