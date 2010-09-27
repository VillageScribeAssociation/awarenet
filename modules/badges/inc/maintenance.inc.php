<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');
	require_once($kapenta->installPath . 'modules/badges/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Badges module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Badges module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function badges_maintenance() {
	global $db, $aliases, $user, $theme;
	if ('admin' != $user->role) { return false; }
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all Badge objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'name', 'error');
	$model = new Badges_Badge();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from Badges_Badge";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('Badges_Badge', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->name, 'non defualt alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('Users_User', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}

		
	} // end while Badges_Badge

	//----------------------------------------------------------------------------------------------
	//	add Badge objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	

	//----------------------------------------------------------------------------------------------
	//	check all UserIndex objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'UID', 'error');
	$model = new Badges_UserIndex();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from Badges_UserIndex";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('Users_User', $model->userUID)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (userUID:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('Badges_Badge', $model->badgeUID)) {
			// TODO: take action here, if possibe assign valid reference to a Badges_Badge
			$errors[] = array($model->UID, $model->UID, 'invalid reference (badgeUID:Badges_Badge)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}

		
	} // end while Badges_UserIndex

	//----------------------------------------------------------------------------------------------
	//	add UserIndex objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
