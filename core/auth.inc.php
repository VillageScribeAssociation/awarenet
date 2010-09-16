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
	global $user, $session; // the current logged in user
	$session->msgAdmin('deprecated: authHas(...) => $user->authHas(...)', 'bug');
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	update permissions for all users from module.xml.php files
//--------------------------------------------------------------------------------------------------
//:	since this could be fairly database intensive, only cron and admin should cause this to happen

function authUpdatePermissions() {
	global $user, $session; // the current logged in user
	$session->msgAdmin('removed: authUpdatePermissions(...) - use user role system.', 'bug');
	return false;
}

?>
