<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Schools module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	maintain the Schools module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function schools_maintenance() {
		global $kapenta;
		global $aliases;
		global $kapenta;
		global $theme;

	if ('admin' != $kapenta->user->role) { return false; }
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all School objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'name', 'error');
	$model = new Schools_School();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from schools_school";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('schools_school', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->name, 'non default alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}

		$range = $aliases->getAll('schools', 'schools_school', $model->UID);
		if (0 == count($range)) {
			$model->save();					//	should create and save a new default alias
			$errorCount++;
			if ('' == trim($model->alias)) {
				$errors[] = array($row['UID'], $row['name'], 'could not add alias');
			} else {
				$errors[] = array($row['UID'], $row['name'], 'added alias');
				$fixCount++;
			}
		}

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('Users_User', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('Users_User', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}

		
	} // end while Schools_School

	//----------------------------------------------------------------------------------------------
	//	add School objects to report
	//----------------------------------------------------------------------------------------------
	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }
	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) { $report .= "<b>Errors Fixed:</b> $fixCount<br/>\n"; }
	

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
