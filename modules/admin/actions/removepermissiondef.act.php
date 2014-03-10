<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	removes a permission definition from some object(module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$modulename = '';		//%	name of a kapenta module [string]
	$modelname = '';		//%	name of object type [string]
	$permission = '';		//%	name of permission on this object [string]

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }
	if (true == array_key_exists('permission', $_POST)) { 
		$permission = $utils->cleanTitle($_POST['permission']); 
	}

	if (true == array_key_exists('module', $kapenta->request->args)) { $modulename = $kapenta->request->args['module']; }
	if (true == array_key_exists('model', $kapenta->request->args)) { $modelname = $kapenta->request->args['model']; }
	if (true == array_key_exists('permission', $kapenta->request->args)) { 
		$permission = $utils->cleanTitle($kapenta->request->args['permission']); 
	}

	if ('' == trim($permission)) { $kapenta->page->do404('Permission not specified.'); }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $kapenta->page->do404('Unkown module.'); }
	if (false == $module->hasModel($modelname)) { $kapenta->page->do404('Unknown model.'); }

	$model = new KModel();
	$model->loadArray($module->models[$modelname]);

	$check = $model->removePermission($permission);
	if (true == $check) {
		$session->msg('Removed permision: ' . $permission, 'ok');
		$module->models[$modelname] = $model->toArray();
		$module->save();

	} else {
		$session->msg('Could not remove permission.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/editmodule/' . $modulename);

?>
