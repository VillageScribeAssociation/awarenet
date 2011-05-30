<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//	maintain the revisons module
//--------------------------------------------------------------------------------------------------

function revisions_maintenance() {
	global $db, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking deleted items table...</h2>";

	$errors = array();
	$errors[] = array('UID', 'refModule', 'refModel', 'refUID', 'status', 'error');

	$sql = "select * from revisions_deleted";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//------------------------------------------------------------------------------------------
		//	if an item is marked as deleted, make sure it is in fact deleted
		//------------------------------------------------------------------------------------------
		if (true == $db->objectExists($row['refModel'], $row['refUID'])) {

			$errors[] = array(
				$row['UID'], $row['refModule'], $row['refModel'], $row['refUID'], $row['status'],
				'object maked as deleted, but not yet removed'
			);

			$sql = "delete from " . $row['refModel'] . " where UID='" . $row['refUID'] . "'";
			$db->query($sql);

			$errorCount++;
			$fixCount++;
		}

		$recordCount++;
	}

	//----------------------------------------------------------------------------------------------
	//	compile report
	//----------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
