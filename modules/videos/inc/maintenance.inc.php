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
	global $kapenta;
	global $db;
	global $aliases;
	global $user;
	global $theme;

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
	$sql = "select * from videos_gallery";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);				// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	checking alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('videos_gallery', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->title, 'non defualt alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}

		$allAliases = $aliases->getAll('videos', 'videos_gallery', $objAry['UID']);
		if (0 == count($allAliases)) {
			$saved = $model->save();									// should reset alias
			$errors[] = array($model->UID, $model->name, 'no alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }			
		}

		//------------------------------------------------------------------------------------------
		//	check share status
		//------------------------------------------------------------------------------------------
		if ('' == $model->shared) {
			$model->shared = 'yes';
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->title, 'Fixed share status.');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:users_user)');
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
	$sql = "select * from videos_video";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('videos_video', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();		// should reset alias
			$errors[] = array($model->UID, $model->title, 'non defualt alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}

		$allAliases = $aliases->getAll('videos', 'videos_video', $objAry['UID']);
		if (0 == count($allAliases)) {
			$saved = $model->save();									// should reset alias
			$errors[] = array($model->UID, $model->name, 'no alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }			
		}

		//------------------------------------------------------------------------------------------
		//	check hash
		//------------------------------------------------------------------------------------------
		if (('' == $model->hash) && (true == $kapenta->fileExists($model->fileName))) {
			$model->save();			// hash is set by $model->vertify()
			$errors[] = array($model->UID, $model->title, 'added hash');
			$errorCount++;
			$fixCount++;
		}

		//------------------------------------------------------------------------------------------
		//	check share status
		//------------------------------------------------------------------------------------------
		if ('' == $model->shared) {
			$model->shared = 'yes';
			$saved = $model->save();			// hash is set by $model->vertify()
			$errors[] = array($model->UID, $model->title, 'Fixed share status');
			$errorCount++;
			if ('' == $saved) { $fixCount++; }
		}

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists($model->refModel, $model->refUID)) {
			// TODO: take action here, if possibe assign valid reference
			$errors[] = array($model->UID, $model->title, 'invalid reference (refModel:refUID)');
			$errorCount++;
		}

		if (false == $db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:users_user)');
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
