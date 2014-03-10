<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the moblog module
//-------------------------------------------------------------------------------------------------

function moblog_maintenance() {
		global $kapenta;
		global $theme;


	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all blog posts
	//----------------------------------------------------------------------------------------------

	$sql = "select * from moblog_post";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		echo "maintain: " . $item['UID'] . ' - ' . $item['title'] . "<br/>\n"; flush();
		$model = new Moblog_Post();
		$model->loadArray($item);
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
