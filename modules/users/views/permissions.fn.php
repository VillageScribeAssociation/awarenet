<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list user permissions
//--------------------------------------------------------------------------------------------------
//opt: module - name of a kapenta module on which permissions may be granted, '*' for all [string]
//opt: model - type of object which permissions may apply, '*' for all [string]
//opt: role - filter to specific role, default is '*' [string]

function users_permissions($args) {
	global $db;
	global $user;
	global $theme;

	$module = '*';			//%	name of a kapenta module, or wildcard [string]
	$model = '*';			//%	name of an object type, or wildcard [string]
	$role = '*';			//%	user role, or wildcard [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	
	if (true == array_key_exists('module', $args)) { $module = $args['module']; }
	if (true == array_key_exists('model', $args)) { $model = strtolower($args['model']); }
	if (true == array_key_exists('role', $args)) { $module = $args['role']; }

	//----------------------------------------------------------------------------------------------
	//	load all roles
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	if ('*' != $role) { $conditions[] = "name='" . $db->addMarkup($role) . "'"; }
	$range = $db->loadRange('users_role', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	filter all permissions and make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Role', 'Type', 'Module', 'Model', 'Permission', 'Condition', '[x]');

	$ns = "<span style='color: #aaa;'><i>none</i></span>";

	foreach($range as $item) {
		$role = new Users_Role($item['UID']);
		foreach($role->permissions->members as $moduleName => $moduleSet) {
			foreach($moduleSet as $permission) {
				$add = true;
	
				$revokeUrl = '%%serverPath%%users/revoke/'
				 . 'role_' . $role->name . '/'
				 . 'module_' . $permission['module'] . '/'
				 . 'model_' . $permission['model'] . '/'
				 . 'permission_' . $permission['permission'] . '/';

				$revokeLink = "<a href='$revokeUrl'>[revoke]</a>";

				if ('*' != $module) {
					if (strtolower($moduleName) != $module) { $add = false; }
				}

				if ('*' != $model) {
					if (strtolower($permission['model']) != $model) { $add = false; }
				}

				if (false == array_key_exists('condition', $permission)) {
					$permission['condition'] = $ns;
				}

				if (true == $add) { 
					$table[] = array(
						$role->name,
						$permission['type'],
						$permission['module'],
						$permission['model'],
						$permission['permission'],
						$permission['condition'],
						$revokeLink
					);
				}
			}
		}
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
