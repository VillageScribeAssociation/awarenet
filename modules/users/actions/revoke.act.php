<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	revoke a permission
//--------------------------------------------------------------------------------------------------
//reqarg: role - name of a user role [string]
//reqarg: module - name of a kapenta module [string]
//reqarg: model - object type [string]
//reqarg: permission - name of a permission applying to this object type [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('role', $req->args)) { $page->do404('Role not given.'); }
	if (false == array_key_exists('module', $req->args)) { $page->do404('Module not given.'); }
	if (false == array_key_exists('model', $req->args)) { $page->do404('Model not given.'); }
	if (false == array_key_exists('permission', $req->args)) { $page->do404('Perm not given.'); }

	$module = $req->args['module'];
	$model = $req->args['model'];
	$permission = $req->args['permission'];

	$role = new Users_Role($req->args['role'], true);
	if (false == $role->loaded) { $page->do404('Unknown role.'); }	

	//----------------------------------------------------------------------------------------------
	//	remove the permission from this role
	//----------------------------------------------------------------------------------------------

	$check = $role->permissions->remove($module, $model, $permission);
	if (true == $check) { $session->msg('Permission revoked.', 'ok'); }
	else { $session->msg('Cound not revoke permission, not found.', 'bad'); }

	$report = $role->save();
	if ('' == $report) { $session->msg('Updated user role.', 'ok'); }
	else { $session->msg('Could not update user role:<br/>' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin console for this module
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/module/' . $module);

?>
