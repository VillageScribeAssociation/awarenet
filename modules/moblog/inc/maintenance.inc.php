<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the moblog
//-------------------------------------------------------------------------------------------------

function maintenance_moblog() {
	global $db, $theme;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking recordAliases...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check that all blog posts are correctly aliased
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'title', 'alias');

	$sql = "select UID, title, alias from Moblog_Post";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$raAll = $aliases->getAll('moblog', 'Moblog_Post', $row['UID']);

		//echo "alias count of moblog '" . $row['title'] . "' is " . count($raAll) . "<br/>\n";

		if (false == $raAll) {
				//---------------------------------------------------------------------------------
				//	no recordAlias for this blog post, create one
				//---------------------------------------------------------------------------------
				$model = new Moblog_Post($row['UID']);
				$model->save();
				$model = new Moblog_Post($row['UID']);
	
				$error = array($row['UID'], $row['title'], $model->alias);
				$errors[] = $error;

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
