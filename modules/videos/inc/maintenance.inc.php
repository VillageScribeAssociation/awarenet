<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');
	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Videos module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Videos module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function videos_maintenance() {
	global $db, $aliases, $user, $theme;
	if ('admin' != $user->role) { return false; }
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all Gallery objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Videos_Gallery();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from Videos_Gallery";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('Videos_Gallery', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->title, 'non defualt alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('Users_User', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}

		
	} // end while Videos_Gallery

	//----------------------------------------------------------------------------------------------
	//	add Gallery objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	

	//----------------------------------------------------------------------------------------------
	//	check all Video objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Videos_Video();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from Videos_Video";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('Videos_Video', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->title, 'non defualt alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('*_*', $model->refUID)) {
			// TODO: take action here, if possibe assign valid reference to a *_*
			$errors[] = array($model->UID, $model->title, 'invalid reference (refUID:*_*)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('Users_User', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}

		
	} // end while Videos_Video

	//----------------------------------------------------------------------------------------------
	//	add Video objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
