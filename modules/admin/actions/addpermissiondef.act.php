<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	creates a new permission definition on a model (module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$modulename = '';		//%	name of a kapenta module [string]
	$modelname = '';		//%	name of object type [string]
	$permission = '';		//%	name of permission on this object [string]
	$export = 'no';			//%	permission applies to owning objects [string]

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }
	if (true == array_key_exists('permission', $_POST)) { 
		$permission = $utils->cleanTitle($_POST['permission']); 
	}

	if ('' == trim($permission)) { $page->do404('Permission not specified.'); }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $page->do404('Unkown module.'); }
	if (false == $module->hasModel($modelname)) { $page->do404('Unknown model.'); }

	$model = new KModel();
	$model->loadArray($module->models[$modelname]);

	$check = $model->addPermission($permission, $export);

	if (true == $check) { 
		$module->models[$modelname] = $model->toArray();
		$module->save();
		$session->msg('Added permission: ' . $permission, 'ok'); 

	} else {
		$session->msg('Could not add permission.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/editmodule/' . $modulename);

?>
