<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//-------------------------------------------------------------------------------------------------
//	maintain the forums module
//-------------------------------------------------------------------------------------------------

function forums_maintenance() {
	global $db, $user, $theme, $aliases;

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking project memberships...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check boards
	//---------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from Forums_Board";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$raAll = $aliases->getAll('forums', 'Forum_Board', $row['UID']);

		//echo "alias count of moblog '" . $row['title'] . "' is " . count($raAll) . "<br/>\n";

		if (false == $raAll) {
				//---------------------------------------------------------------------------------
				//	no recordAlias for this blog post, create one
				//---------------------------------------------------------------------------------
				$model = new Forums_Board($row['UID']);
				$model->save();
	
				$error = array($row['UID'], $row['title'], $model->alias);
				$errors[] = $error;

				$fixCount++;
				$errorCount++;
		}
		$recordCount++;


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



	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report = "<h2>Checking project memberships...</h2>";

	//---------------------------------------------------------------------------------------------
	//	check boards
	//---------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from Forums_Thread";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$raAll = $aliases->getAll('forums', 'Forums_Thread', $row['UID']);

		//echo "alias count of moblog '" . $row['title'] . "' is " . count($raAll) . "<br/>\n";

		if (false == $raAll) {
				//---------------------------------------------------------------------------------
				//	no recordAlias for this blog post, create one
				//---------------------------------------------------------------------------------
				$model = new Forums_Thread($row['UID']);
				$model->save();
	
				$error = array($row['UID'], $row['title'], $model->alias);
				$errors[] = $error;

				$fixCount++;
				$errorCount++;
		}
		$recordCount++;


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
