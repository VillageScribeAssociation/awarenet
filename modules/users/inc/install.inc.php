<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/session.mod.php');
	require_once($kapenta->installPath . 'core/kmodule.class.php');
	
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
	global $kapenta;
	
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
	//	create or upgrade Users_Session table
	//----------------------------------------------------------------------------------------------
	$model = new Users_Session();
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
	//	create default user roles if they do not already exist
	//----------------------------------------------------------------------------------------------
	$roles = array('admin', 'public', 'student', 'teacher', 'banned');
	
	foreach($roles as $roleName) {
	$model = new Users_Role($roleName, true);
		if (false == $model->loaded) {
			$model->UID = $kapenta->createUID();
			$model->name = $roleName;
			$check = $model->save();
			if ('' == $check) { $report .= "Created '" . $roleName . "' user group.<br/>\n"; }
			else { $report .= "Could not create '" . $roleName . "' user group.<br/>\n"; }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	set default permissions as specified by modules
	//----------------------------------------------------------------------------------------------
	
	$modList = $kapenta->listModules();
	foreach($modList as $modName) {
		$mod = new KModule($modName);
		if (false == $mod->loaded) {
			$report .= "Could not load module: $modName<br/>\n";
		} else {
			//--------------------------------------------------------------------------------------		
			//	
			//--------------------------------------------------------------------------------------
			$report .= ''
				. "module: $modName <br/>\n"
				. "description: " . $mod->description . "<br/>\n"
				. "Default permissions: " . count($mod->defaultpermissions) . "<br/>\n";

			foreach($mod->defaultpermissions as $defperm) { 
				$parts = explode(':', trim($defperm), 2);
				$args = explode('|', $parts[1] . '|||||||');
				$role = new Users_Role($parts[0], true);
				if (true == $role->loaded) { 
					$added = $role->permissions->add(
						$args[1], $args[2], $args[3], $args[5]
					);
					if (true == $added) {
						$report .= "added permission: {$parts[1]} ({$parts[0]}) <br/>\n"; 
					} else { 
						$report .= "could not add default permission: $defperm<br/>\n";
					}

					$check = $role->save();
					if ('' != $check) { $report .= "Could not save role: $check <br/>\n"; }

				} else { $report .= "Could not load role: " . $parts[0] . "<br/>\n"; }

			}

		}

	}
	
	//----------------------------------------------------------------------------------------------
	//	create default admin account from windows installer if it doesn't already exist
	//----------------------------------------------------------------------------------------------
	$adminUID = $registry->get('firstrun.adminuid');
	$adminUser = $registry->get('firstrun.adminuser');
	$adminPass = $registry->get('firstrun.adminpass');
	$firstSchool = $registry->get('firstrun.firstschool');
	
	if (('' !== $adminUID) && ('' !== $adminUser) && ('' !== $adminPass)) {
		if (false == $db->objectExists('users_user', $adminUID)) {
			$model = new Users_User();
			$model->UID = $adminUID;
			$model->role = 'admin';
			if ('' !== $firstSchool) { $model->school = $firstSchool; }
			$model->grade = 'Staff';
			$model->firstname = 'System';
			$model->surname = 'Administrator';
			$model->username = $adminUser;
			$model->password = sha1($adminPass . $adminUID);
			$model->lang = 'en';
			$check = $model->save();
			if ('' == $check) {
				$report .= "Created default admin account ($adminUID).<br/>\n";
			} else {
				$report .= "Could not create default administrator account:<br/>\n$check<br/>\n";
			}
		}
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
	//	ensure the table which stores Session objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Users_Session();
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
