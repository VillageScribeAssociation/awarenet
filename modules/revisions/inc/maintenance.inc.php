<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintain the revisons module
//--------------------------------------------------------------------------------------------------

function revisions_maintenance() {
		global $db;
		global $theme;


	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$errors = array();
	$errors[] = array('UID', 'refModule', 'refModel', 'refUID', 'status', 'error');

	//----------------------------------------------------------------------------------------------
	//	check revisions_deleted table
	//----------------------------------------------------------------------------------------------
	$report = "<h2>Checking deleted items table...</h2>";
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

	echo "checked revisions<br/>\n"; flush();

	//----------------------------------------------------------------------------------------------
	//	check revisions_revision table
	//----------------------------------------------------------------------------------------------
	$report .= "<h2>Checking revisions table...</h2>";
	$sql = "select * from revisions_revision";
	$result = $db->query($sql);

	$badValues = array('varchar(30)', 'varchar(33)');

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//------------------------------------------------------------------------------------------
		//	check the refModel
		//------------------------------------------------------------------------------------------

		if (('revisions_revision' == $row['refModel']) || ('p2p_gift' == $row['refModel'])) {
			$errors[] = array(
				$row['UID'], 'n/a', $row['refModel'], $row['refUID'], $row['refUID'],
				'bad refModel: ' . $row['refModel'] . '.'
			);

			$sql = "delete from revisions_revision where UID='" . $row['UID'] . "'";
			$db->query($sql);

			$errorCount++;
			$fixCount++;
		}

		//------------------------------------------------------------------------------------------
		//	check the refUID
		//------------------------------------------------------------------------------------------
		if (true == in_array(strtolower($row['refUID']), $badValues)) {

			$errors[] = array(
				$row['UID'], 'n/a', $row['refModel'], $row['refUID'], $row['refUID'],
				'bad reference value.'
			);

			$sql = "delete from revisions_revision where UID='" . $row['UID'] . "'";
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
