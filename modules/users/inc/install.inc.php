<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/login.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Users module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Users module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function users_install_module() {
	global $db;
	global $user;
	global $registry;

	if ('admin' != $user->role) { return false; }
	$dba = new KDBAdminDriver();
	$report = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Users_Friendship table
	//----------------------------------------------------------------------------------------------
	$model = new Users_Friendship();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous friendships table
	//----------------------------------------------------------------------------------------------
	$rename = array();
	$count = $dba->copyAll('friendships', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'friendships' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Users_Login table
	//----------------------------------------------------------------------------------------------
	$model = new Users_Login();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Users_Role table
	//----------------------------------------------------------------------------------------------
	$model = new Users_Role();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Users_User table
	//----------------------------------------------------------------------------------------------
	$model = new Users_User();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous users table
	//----------------------------------------------------------------------------------------------
	$rename = array('ofGroup' => 'role', 'recordAlias' => 'alias');
	$count = $dba->copyAll('users', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'users' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create default registry values
	//----------------------------------------------------------------------------------------------
	if ('' == $registry->get('users.allowpublicsignup')) { 
		$registry->set('users.allowpublicsignup', 'no');
	}

	if ('' == $registry->get('users.allowteachersignup')) { 
		$registry->set('users.allowteachersignup', 'no');
	}

	if ('' == $registry->get('users.grades')) { 
		$grades = array(
			'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7',
			'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12', '1. Klasse', '2. Klasse',
			'3. Klasse', '4. Klasse', '5. Klasse', '6. Klasse', '7. Klasse', '8. Klasse',
			'9. Klasse', '10. Klasse', '11. Klasse', '12. Klasse', '13. Klasse', 'Std. 1',
			'Std. 2', 'Std. 3', 'Std. 4', 'Std. 5', 'Std. 6', 'Std. 7', 'Std. 8', 'Std. 9',
			'Std. 10', 'Std. 11', 'Std. 12', 'Alumni', 'Staff'
		);	// add other school systems here

		$gradeStr = implode("\n", $grades);
		$registry->set('users.grades', $gradeStr);
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function users_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$dba = new KDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Friendship objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Users_Friendship();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Role objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Users_Role();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Login objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Users_Login();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores User objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Users_User();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
