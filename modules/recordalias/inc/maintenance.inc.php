<?

//-------------------------------------------------------------------------------------------------
//	maintain the recordAlias table
//-------------------------------------------------------------------------------------------------

function maintenance_recordalias() {
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking reacordalias table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check that all recordaliases are for an existing record
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'refTable', 'refUID', 'alias', 'error');

	$tables = dbListTables();

	$sql = "select * from recordalias";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);

		//-----------------------------------------------------------------------------------------
		//	check that refTable and refUID exist
		//-----------------------------------------------------------------------------------------
		$allGood = true;
		if (in_array($row['refTable'], $tables) == false) { $allGood = false; } 
		else {
			if (dbRecordExists($row['refTable'], $row['refUID']) == false) { $allGood = false; }
		}
		
		//-----------------------------------------------------------------------------------------
		//	delete dud records
		//-----------------------------------------------------------------------------------------
		if (false == $allGood) {
			$msg = "nosuch";
			$error = array($row['UID'], $row['refTable'], $row['refUID'], $row['alias'], $msg);
			$errors[] = $error;

			dbDelete('recordalias', $row['UID']);
			$fixCount++;
			$errorCount++;
		}

		$recordCount++;
	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
