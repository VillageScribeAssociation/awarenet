<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the users module
//-------------------------------------------------------------------------------------------------

function users_maintenance() {
	global $db;
	global $kapenta;
	global $theme;
	global $aliases;

	$report = '';						//%	return value [string]

	//---------------------------------------------------------------------------------------------
	//	check all users_user
	//---------------------------------------------------------------------------------------------
	$report .= "<h2>Checking users_user table...</h2>";

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;

	$errors = array();
	$errors[] = array('UID', 'Title', 'error');

	$sql = "select * from users_user";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Users_User();
		$model->loadArray($row);

		//-----------------------------------------------------------------------------------------
		//	check that this record has an alias
		//-----------------------------------------------------------------------------------------
		if ('' == trim($row['alias'])) {
			$model->save();
			$errorCount++;
			$model->load($row['UID']);
			if ('' == trim($model->alias)) {
				// not fixed
				$errors[] = array($row['UID'], $row['username'], 'could not add alias');
			} else {
				// fixed
				$errors[] = array($row['UID'], $row['username'], 'added alias');
				$fixCount++;
			}
		}

		$range = $aliases->getAll('users', 'users_user', $model->UID);
		if (0 == count($range)) {
			$model->save();					//	should create and save a new default alias
			$errorCount++;
			if ('' == trim($model->alias)) {
				$errors[] = array($row['UID'], $row['username'], 'could not add alias');
			} else {
				$errors[] = array($row['UID'], $row['username'], 'added alias');
				$fixCount++;
			}
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

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------\
	return $report;
}

?>
