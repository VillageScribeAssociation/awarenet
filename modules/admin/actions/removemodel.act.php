<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	delete a model definition (module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not apecified.'); }
	if ('deleteModel' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }

	$modulename = '';
	$modelname = '';

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $kapenta->page->do404('Unkown module'); }
	if (false == $module->hasModel($modelname)) { $kapenta->page->do404('Unkown model'); }

	//----------------------------------------------------------------------------------------------
	//	remove the model
	//----------------------------------------------------------------------------------------------
	
	$check = $module->removeModel($modelname);

	if (true == $check) {
		$module->save();
		$kapenta->session->msg('Removed model: ' . $modelname, 'ok');
	} else {
		$kapenta->session->msg('Could not remove model.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('admin/editmodule/' . $modulename);

?>
