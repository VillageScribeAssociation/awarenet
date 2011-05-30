<?

	require_once($kapenta->installPath . 'modules/aliases/models/aliad.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the recordAlias table
//-------------------------------------------------------------------------------------------------

function alias_maintenace() {
	global $db, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking aliases_alias table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check that all recordaliases are for an existing record
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'refTable', 'refUID', 'alias', 'error');

	$db->loadTables();
	$tables = $db->tables;

	$sql = "select * from aliases_alias";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//-----------------------------------------------------------------------------------------
		//	check that refTable and refUID exist
		//-----------------------------------------------------------------------------------------
		$allGood = true;
		if (false == in_array($row['refTable'], $tables)) { $allGood = false; } 
		else {
			if (false == $db->objectExists($row['refTable'], $row['refUID'])) { $allGood = false; }
		}
		
		//-----------------------------------------------------------------------------------------
		//	delete dud records
		//-----------------------------------------------------------------------------------------
		if (false == $allGood) {
			$msg = "nosuch";
			$error = array($row['UID'], $row['refTable'], $row['refUID'], $row['alias'], $msg);
			$errors[] = $error;

			$model = new Aliases_Alias();
			$model->loadArray($row);
			$model->delete();

			$fixCount++;
			$errorCount++;
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
