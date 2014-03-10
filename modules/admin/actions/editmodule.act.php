<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//*	page for editing a module definition
//--------------------------------------------------------------------------------------------------
//+	this is mostly to ease development and should be removed in production versions

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }		// admins only

	$moduleName = $kapenta->request->ref;
	if (false == $kapenta->moduleExists($moduleName)) { $kapenta->page->do404(); }

	$module = new KModule($moduleName);
	if (false == $module->loaded) {
		//------------------------------------------------------------------------------------------
		//	create module definition file if none exists
		//------------------------------------------------------------------------------------------
		$module->modulename = $moduleName;
		$module->version = '1';
		$module->revision = '0';
		$module->description = 'Describe your module here';
		$module->save();

		$session->msg("Creating module definition: $moduleName", 'ok');
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/admin/actions/editmodule.page.php');
	$kapenta->page->blockArgs['modulename'] = $moduleName;
	$kapenta->page->blockArgs['xmodule'] = $moduleName;
	$kapenta->page->render();

?>
