<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Groups module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	maintain the Groups module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function groups_maintenance() {
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
	//	check all Group objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Groups_Group();
	$dbSchema = $model->getDbSchema();
	$sql = "select * from groups_group";
	$handle = $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objAry);			// remove database markup
		$model = new Groups_Group($objAry['UID']);
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check alias
		//------------------------------------------------------------------------------------------
		$defaultAlias = $aliases->getDefault('groups_group', $objAry['UID']);
		if ((false == $defaultAlias) || ($defaultAlias != $model->alias)) {
			$saved = $model->save();									// should reset alias
			$errors[] = array($model->UID, $model->name, 'non default alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }
		}

		$allAliases = $aliases->getAll('groups', 'groups_group', $objAry['UID']);
		if (0 == count($allAliases)) {
			$saved = $model->save();									// should reset alias
			$errors[] = array($model->UID, $model->name, 'no alias');
			$errorCount++;
			if (true == $saved) { $fixCount++; }			
		}

		//------------------------------------------------------------------------------------------
		//	check associations with schools
		//------------------------------------------------------------------------------------------
		$model->updateSchoolsIndex();

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->name, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Groups_Group

	//----------------------------------------------------------------------------------------------
	//	add Groups_Group objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	
	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
