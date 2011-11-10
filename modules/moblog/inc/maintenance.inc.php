<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the moblog module
//-------------------------------------------------------------------------------------------------

function moblog_maintenance() {
	global $db, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all blog posts
	//----------------------------------------------------------------------------------------------

	$sql = "select * from moblog_post";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Moblog_Post();
		$model->loadARray($row);
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
