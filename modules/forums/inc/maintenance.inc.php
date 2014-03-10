<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintain the forums module
//--------------------------------------------------------------------------------------------------

function forums_maintenance() {
		global $kapenta;
		global $user;
		global $theme;
		global $aliases;

	$report = '';	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check boards
	//----------------------------------------------------------------------------------------------
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report .= "<h2>Checking boards ...</h2>";

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from forums_board";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$raAll = $aliases->getAll('forums', 'forums_board', $row['UID']);

		//------------------------------------------------------------------------------------------
		//	check alias
		//------------------------------------------------------------------------------------------
		if (false == $raAll) {
			//--------------------------------------------------------------------------------------
			//	board has no alias, create one
			//--------------------------------------------------------------------------------------
			$model = new Forums_Board($row['UID']);
			$model->save();
	
			$error = array($row['UID'], $row['title'], $model->title . " (set alias)");
			$errors[] = $error;

			$fixCount++;
			$errorCount++;
		}

		//------------------------------------------------------------------------------------------
		//	check weight
		//------------------------------------------------------------------------------------------
		if ('' == $row['weight']) {
			$model = new Forums_Board($row['UID']);
			$model->weight = 0;
			$model->save();

			$error = array($row['UID'], $row['title'], "set weight to 0 (" . $model->alias . ")");
			$errors[] = $error;

			$fixCount++;
			$errorCount++;
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


	//----------------------------------------------------------------------------------------------
	//	check threads
	//----------------------------------------------------------------------------------------------

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$report .= "<h2>Checking threads...</h2>";

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "SELECT * from forums_thread";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$raAll = $aliases->getAll('forums', 'forums_thread', $row['UID']);

		//------------------------------------------------------------------------------------------
		//	check thread aliases
		//------------------------------------------------------------------------------------------
		if (false == $raAll) {
			//--------------------------------------------------------------------------------------
			//	no alais for this thread, create one
			//--------------------------------------------------------------------------------------
			$model = new Forums_Thread($row['UID']);
			$model->save();
	
			$error = array($row['UID'], $row['title'], $model->alias);
			$errors[] = $error;

			$fixCount++;
			$errorCount++;
		}

		//------------------------------------------------------------------------------------------
		//	check reply counts
		//------------------------------------------------------------------------------------------
		$conditions = array("thread='" . $kapenta->db->addMarkup($row['UID']) . "'");
		$numReplies = $kapenta->db->countRange('forums_reply', $conditions);

		if ($numReplies != (int)$row['replies']) { 
			$model = new Forums_Thread($row['UID']);
			$model->replies = $numReplies;
			$model->save();
	
			$error = array($row['UID'], $row['title'], 'Set reply count to ' . $numReplies);
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
