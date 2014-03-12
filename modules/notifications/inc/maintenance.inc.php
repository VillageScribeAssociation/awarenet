<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');
	require_once($kapenta->installPath . 'modules/notifications/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	maintenance script for Notifications module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Notifications module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function notifications_maintenance() {
	global $theme;

		global $kapenta;
		global $aliases;
		global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }
	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	check all Notification objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'title', 'error');
	$model = new Notifications_Notification();
	$dbSchema = $model->getDbSchema();
	$sql .= "select * from notifications_notification";
	$handle .= $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('*_*', $model->refUID)) {
			// TODO: take action here, if possibe assign valid reference to a *_*
			$errors[] = array($model->UID, $model->title, 'invalid reference (refUID:*_*)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('images_image', $model->imageUID)) {
			// TODO: take action here, if possibe assign valid reference to a Images_Image
			$errors[] = array($model->UID, $model->title, 'invalid reference (imageUID:images_image)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->title, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Notifications_Notification

	//----------------------------------------------------------------------------------------------
	//	add Notification objects to report
	//----------------------------------------------------------------------------------------------
	$report .= $theme->arrayToHtmlTable($errors, true, true);
	

	//----------------------------------------------------------------------------------------------
	//	check all UserIndex objects
	//----------------------------------------------------------------------------------------------
	$errors = array();
	$errors[] = array('UID', 'UID', 'error');
	$model = new Notifications_UserIndex();
	$dbSchema = $model->getDbSchema();
	$sql .= "select * from notifications_userindex";
	$handle .= $kapenta->db->query($sql);

	while ($objAry = $kapenta->db->fetchAssoc($handle)) {
		$objAry = $kapenta->db->rmArray($objArray);		// remove database markup
		$model->loadArray($objAry);		// load into model
		$recordCount++;

		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->db->objectExists('users_user', $model->userUID)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (userUID:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('notifications_notification', $model->notificationUID)) {
			// TODO: take action here, if possibe assign valid reference to a Notifications_Notification
			$errors[] = array($model->UID, $model->UID, 'invalid reference (notificationUID:notifications_notification)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (createdBy:users_user)');
			$errorCount++;
		}

		if (false == $kapenta->db->objectExists('users_user', $model->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$errors[] = array($model->UID, $model->UID, 'invalid reference (editedBy:users_user)');
			$errorCount++;
		}

		
	} // end while Notifications_UserIndex

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
