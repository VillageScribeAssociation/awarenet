<?

//--------------------------------------------------------------------------------------------------
//*	functions releated to checking and managing user permissions
//--------------------------------------------------------------------------------------------------
//+	notes:
//+	global $user object has an array for permissions for each module of the form
//+	[type][module][permission][condition]
//+
//+	condition is something like %%user.ofGroup%%=admin or %%user.username%%=%%data.createdBy%%
//+	each side is evaluated and if they match the permission is granted.
//+
//+	permissions can be of type 'auto' (applying to all users based on module settings) or type
//+	'special', existing only in user records where a permission has been explicitly granted, 
//+	for example:
//+
//+	|special|forum|moderater|%%data.forum%%=cheese|  -  this user is a moderator on a forum

//--------------------------------------------------------------------------------------------------
//|	check if a user has a given permission
//--------------------------------------------------------------------------------------------------
//arg: module - module name [string]
//arg: permission - permission name [string]
//arg: data - reserved [array]
//: data array is to provide additional context in an expanded permission system, as yet unused

function authHas($module, $permission, $data) {
	global $user; // the current logged in user
	if ($user->data['ofGroup'] == 'admin') { return true; }
	if (array_key_exists($module, $user->permissions) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	evaluate each permission this user has on this module
	//----------------------------------------------------------------------------------------------

	foreach($user->permissions[$module] as $perm) {
	  if ($perm['permission'] == $permission) {

		$parts = explode("=", $perm['condition']);
		$condition1 = $parts[0];
		$condition2 = $parts[1];

		$condition1 = str_replace('%%user.UID%%', $user->data['UID'], $condition1);
		$condition1 = str_replace('%%user.username%%', $user->data['username'], $condition1);
		$condition1 = str_replace('%%user.ofGroup%%', $user->data['ofGroup'], $condition1);

		$condition2 = str_replace('%%user.UID%%', $user->data['UID'], $condition2);
		$condition2 = str_replace('%%user.username%%', $user->data['username'], $condition2);
		$condition2 = str_replace('%%user.ofGroup%%', $user->data['ofGroup'], $condition2);
		/*
		if (is_array($data)) {
			foreach($data as $key => $val) {
				$condition1 = str_replace('%%data.' . $key . '%%', $val, $conditon1);
				$condition2 = str_replace('%%data.' . $key . '%%', $val, $conditon2);
			}
		} */

		if (trim($condition1) == trim($condition2)) { return true; }		

	  }
	}

	return false;
}

//--------------------------------------------------------------------------------------------------
//|	update permissions for all users from module.xml.php files
//--------------------------------------------------------------------------------------------------
//:	since this could be fairly database intensive, only cron and admin should cause this to happen

function authUpdatePermissions() {
	require_once($installPath . 'modules/users/models/user.mod.php');
	require_once($installPath . 'modules/admin/models/kmodule.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load all module.xml files into KModule objects, collect permissions in array
	//----------------------------------------------------------------------------------------------

	$allMods = array();
	$modList = listModules();
	foreach($modList as $modName) {
		$m = new KModule($modName);
		$allMods[$modName] = $m->permissions;
	}

	//----------------------------------------------------------------------------------------------
	//	for each user record, save special permissions while refreshing auto ones
	//----------------------------------------------------------------------------------------------

	$sql = 'select * from users';
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$newUserPerms = '';
		$row = sqlRMArray($row);
		$u = new User();
		$u->loadArray($row);

		// recreate special permssions (explicitly granted by admin)
		foreach($u->permissions as $uPerm) {
		  	if ($uPerm['type'] == 'special') {
				$newUserPerms .= '|special|' . $uPerm['module'] . '|' . $uPerm['permission'] 
								. '|' . $uPerm['condition'] . "|\n";
		  	}
		}

		// add all auto permissions (as stored in module.xml.php files)
		foreach($allMods as $modName => $modPerm) {
		  foreach($modPerm as $permName => $conditions) {
			foreach($conditions as $condition) {
				$newUserPerms .= '|auto|' . $modName . '|' . $permName . '|' . $condition . "|\n";
			}
		  }
		}

		$newUserPerms = trim($newUserPerms);

		if ($u->data['permissions'] != $newUserPerms) {
			$u->data['permissions'] = $newUserPerms;
			$u->save();
		}
	}
}

?>
