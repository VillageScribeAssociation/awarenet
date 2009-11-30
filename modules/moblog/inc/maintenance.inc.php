<?

//-------------------------------------------------------------------------------------------------
//	maintain the moblog
//-------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/moblog/models/moblog.mod.php');

function maintenance_moblog() {
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking recordAliases...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check that all blog posts are correctly aliased
	//---------------------------------------------------------------------------------------------

	$errors = array();
	$errors[] = array('UID', 'title', 'alias');

	$sql = "select UID, title, recordAlias from moblog";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);

		$raAll = raGetAll('moblog', $row['UID']);

		//echo "alias count of moblog '" . $row['title'] . "' is " . count($raAll) . "<br/>\n";

		if (false == $raAll) {
				//---------------------------------------------------------------------------------
				//	no recordAlias for this blog post, create one
				//---------------------------------------------------------------------------------
				$model = new Moblog($row['UID']);
				$model->save();
				$model = new Moblog($row['UID']);
	
				$error = array($row['UID'], $row['title'], $model->data['recordAlias']);
				$errors[] = $error;

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
