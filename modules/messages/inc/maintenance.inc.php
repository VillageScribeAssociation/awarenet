<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Messages module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	Messages module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function messages_maintenance() {
	global $db, $aliases, $user, $theme;
	if ('admin' != $user->role) { return false; }
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all Message objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Messages_Message();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from messages_message";
	$handle = $db->query($sql);

	while ($objAry = $db->fetchAssoc($handle)) {
		$objAry = $db->rmArray($objAry);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		/*
		if (false == $db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:Users_User)');
			$errorCount++;
		}

		if (false == $db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:Users_User)');
			$errorCount++;
		}
		*/
		
		//------------------------------------------------------------------------------------------
		//	check that fromName and toName are set
		//------------------------------------------------------------------------------------------
		if ('' == $model->fromName) {
			$nameBlock = '[[:users::name::userUID=' . $model->fromUID . ':]]';
			$fromName = $theme->expandBlocks($nameBlock, '');
			$model->fromName = $fromName;
			$model->save();
			$errors[] = array($model->UID, $model->title, 'missing fromName');
			$errorCount++;			
			$fixCount++;
		}

		if ('' == $model->toName) {
			$nameBlock = '[[:users::name::userUID=' . $model->toUID . ':]]';
			$toName = $theme->expandBlocks($nameBlock, '');
			$model->toName = $toName;
			$model->save();
			$errors[] = array($model->UID, $model->title, 'missing toName');
			$errorCount++;			
			$fixCount++;
		}

	} // end while Messages_Message

	//----------------------------------------------------------------------------------------------
	//	add Message objects to report
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
