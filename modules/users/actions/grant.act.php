<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	grant a permission to a user role
//--------------------------------------------------------------------------------------------------
//postarg: module - name of a kapenta module [string]
//postarg: model - object type [string]
//postarg: role - a user role [string]
//postarg: permission - name of permission to be granted [string]
//postopt: relationship - relationship on which permission may depend [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role (admins only)
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('module', $_POST)) { $kapenta->page->do404('Module not specified'); }
	if (false == array_key_exists('model', $_POST)) { $kapenta->page->do404('Model not specified'); }
	if (false == array_key_exists('role', $_POST)) { $kapenta->page->do404('Role not given'); }
	if (false == array_key_exists('permission', $_POST)) { $kapenta->page->do404('Permission not given'); }

	$module = $_POST['module'];
	$model = $_POST['model'];
	$permission = $_POST['permission'];
	$relationship = '';
	
	$role = new Users_Role($_POST['role'], true);
	if (false == $role->loaded) { $kapenta->page->do404('Unknown role.'); }

	if (true == array_key_exists('relationship', $_POST)) {
		$relationship = $_POST['relationship']; 
	}

	if (false == $kapenta->moduleExists($module)) { $kapenta->page->do404('Unknown module.'); }

	//TODO: more checks here

	//----------------------------------------------------------------------------------------------
	//	grant the permission
	//----------------------------------------------------------------------------------------------
	$role->permissions->add($module, $model, $permission, $relationship);
	$report = $role->save();

	if ('' == $report) { $session->msg('Updated role with new permission.', 'ok'); }
	else { $session->msg('Could not update role:<br/>' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to admin module page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->do302('admin/module/' . $module);

?>
