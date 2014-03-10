<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//*	add a model to a module definition (module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check post vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not spciefid.'); }
	if ('addModel' != $_POST['action']) { $kapenta->page->do404('Unkown action.'); }

	$modulename = '';
	$modelname = '';

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $kapenta->page->do404('unknown module'); }
	if ('' == trim($modelname)) { $kapenta->page->do404('Model name not given.'); }

	if (true == array_key_exists($modelname, $module->models)) {
		$kapenta->page->do404('Model already exists.');
	}

	//----------------------------------------------------------------------------------------------
	//	add the model
	//----------------------------------------------------------------------------------------------
	$model = new KModel();
	$model->name = $modelname;

	$module->models[$modelname] = $model->toArray();
	$module->save();

	//----------------------------------------------------------------------------------------------
	//	redirect back to editmodule
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/editmodule/' . $modulename);	

?>
