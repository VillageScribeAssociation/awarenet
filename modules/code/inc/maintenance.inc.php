<?php

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the images module
//-------------------------------------------------------------------------------------------------

function code_maintenance() {
	global $db, $kapenta, $theme, $aliases;
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking code_file table...</h2>";

	//----------------------------------------------------------------------------------------------
	//	load set of packages
	//----------------------------------------------------------------------------------------------
	$packages = $db->loadRange('code_package', 'UID, name', array());

	//----------------------------------------------------------------------------------------------
	//	check file inheritance and package
	//----------------------------------------------------------------------------------------------
	$totalSize;
	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "select UID, package, parent, LENGTH(content) as filelen from code_file";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Code_File($row['UID']);

		//-----------------------------------------------------------------------------------------
		//	check that this file belongs to an extand package
		//-----------------------------------------------------------------------------------------
		if ('' == $model->package) {
			//$db->delete($model->UID, $model->dbSchema);
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'No package.');
		}

		$packageFound = false;
		foreach($packages as $pkg) {
			if ($model->package == $pkg['UID']) { $packageFound = true; }
		}

		if (false == $packageFound) {
			$db->delete($model->UID, $model->dbSchema);
			$errorCount++;
			$errors[] = array($row['UID'], $row['title'], 'Unknown package.');
		}

		//-----------------------------------------------------------------------------------------
		//	check inheritance
		//-----------------------------------------------------------------------------------------

		if (($model->parent != '') && ($model->parent != 'root')) {
			$parent = new Code_File($model->parent);
			if (false == $parent->loaded) {
				//$db->delete($model->UID, $model->dbSchema);
				$errorCount++;
				$errors[] = array($row['UID'], $row['title'], 'Unknown parent (' . $model->parent . ').');
			}
		}

		$totalSize += $row['filelen'];

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

	$report .= "Total file sizes: $totalSize (bytes)<br/>\n";

	return $report;
}

?>
