<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	update a model definintion in a module.xml.php file
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$modulename = '';				//%	name of a kapenta module [string]
	$modelname = '';				//%	name of object type [string]

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified'); }
	if ('saveModel' != $_POST['action']) { $kapenta->page->do404('Action not supported'); }

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $kapenta->page->do404('Unkown module.'); }

	//----------------------------------------------------------------------------------------------
	//	update the model definition
	//----------------------------------------------------------------------------------------------
	$model = new KModel();
	$model->loadArray($module->models[$modelname]);
	
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'name':			$model->name = $utils->cleanTitle($value);			break;
			case 'modelname':		$model->name = $utils->cleanTitle($value);			break;
			case 'description':		$model->description = $value;						break;
		}
	}

	$module->models[$modelname] = $model->toArray();
	$module->save();

	$kapenta->session->msg('Updated module definiton.', 'ok');

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/editmodule/' . $modulename);

?>
