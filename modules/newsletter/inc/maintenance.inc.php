<?

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Newsletter module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	maintain the Newsletter module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function newsletter_maintenance() {
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
	//	check all Category objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'name', 'error');
	$model = new Newsletter_Category();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from newsletter_category";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('newsletter_category', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->name, 'non default alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->name, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->name, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Newsletter_Category

	//----------------------------------------------------------------------------------------------
	//	add Category objects to report
	//----------------------------------------------------------------------------------------------
	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }
	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) { $report .= "<b>Errors Fixed:</b> $fixCount<br/>\n"; }
	

	//----------------------------------------------------------------------------------------------
	//	check all Edition objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'subject', 'error');
	$model = new Newsletter_Edition();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from newsletter_edition";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('newsletter_edition', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->subject, 'non default alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->subject, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->subject, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Newsletter_Edition

	//----------------------------------------------------------------------------------------------
	//	add Edition objects to report
	//----------------------------------------------------------------------------------------------
	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }
	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) { $report .= "<b>Errors Fixed:</b> $fixCount<br/>\n"; }
	

	//----------------------------------------------------------------------------------------------
	//	check all Notice objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Newsletter_Notice();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from newsletter_notice";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Newsletter_Notice

	//----------------------------------------------------------------------------------------------
	//	add Notice objects to report
	//----------------------------------------------------------------------------------------------
	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }
	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) { $report .= "<b>Errors Fixed:</b> $fixCount<br/>\n"; }
	

	//----------------------------------------------------------------------------------------------
	//	check all Subscription objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'UID', 'error');
	$model = new Newsletter_Subscription();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from newsletter_subscription";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->UID, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a users_user
			$errors[] = array($model->UID, $model->UID, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Newsletter_Subscription

	//----------------------------------------------------------------------------------------------
	//	add Subscription objects to report
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
