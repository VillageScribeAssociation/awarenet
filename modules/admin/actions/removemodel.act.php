<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	delete a model definition (module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not apecified.'); }
	if ('deleteModel' != $_POST['action']) { $page->do404('Action not supported.'); }

	$modulename = '';
	$modelname = '';

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $page->do404('Unkown module'); }
	if (false == $module->hasModel($modelname)) { $page->do404('Unkown model'); }

	//----------------------------------------------------------------------------------------------
	//	remove the model
	//----------------------------------------------------------------------------------------------
	
	$check = $module->removeModel($modelname);

	if (true == $check) {
		$module->save();
		$session->msg('Removed model: ' . $modelname, 'ok');
	} else {
		$session->msg('Could not remove model.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/editmodule/' . $modulename);

?>
