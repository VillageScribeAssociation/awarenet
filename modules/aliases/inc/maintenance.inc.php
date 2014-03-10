<?

	require_once($kapenta->installPath . 'modules/aliases/models/alias.mod.php');

//--------------------------------------------------------------------------------------------------
//|	maintain the aliases module
//--------------------------------------------------------------------------------------------------

function aliases_maintenance() {
	global $kapenta;
	global $revisions;
	global $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking aliases_alias table...</h2>";

	//----------------------------------------------------------------------------------------------
	//	check that all aliases are for an existing record
	//----------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'refModel', 'refUID', 'alias', 'error');

	$kapenta->db->loadTables();
	$tables = $db->tables;

	$sql = "select * from aliases_alias";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);

		//------------------------------------------------------------------------------------------
		//	check that refModel and refUID exist
		//------------------------------------------------------------------------------------------
		$allGood = true;
		if (false == in_array($row['refModel'], $tables)) {
			//$allGood = false;
		} else {
			if (true == $revisions->isDeleted($row['refModel'], $row['refUID'])) {
				$allGood = false;
			}
		}
		
		//------------------------------------------------------------------------------------------
		//	delete aliases for obejcts which have been deleted
		//------------------------------------------------------------------------------------------
		if (false == $allGood) {
			$msg = "nosuch";
			$error = array($row['UID'], $row['refModel'], $row['refUID'], $row['alias'], $msg);
			$errors[] = $error;

			$model = new Aliases_Alias();
			$model->loadArray($row);
			$model->delete();

			$fixCount++;
			$errorCount++;
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
