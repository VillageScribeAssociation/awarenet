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
	$report = '';


	$report .= "<h2>Checking home_static table...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check all static pages
	//---------------------------------------------------------------------------------------------
	$sql = "select * from home_static";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		$model = new Home_Static();
		$model->loadArray($row);
		$notes = $model->maintain();

		foreach($notes as $note) { 
			$report .= "<div class='inlinequote'>$note</div>\n";
			if (false != strpos($note, '<!-- error -->')) { $errorCount++; }
			if (false != strpos($note, '<!-- fixed -->')) { $fixCount++; }
		}

		$recordCount++;
	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
