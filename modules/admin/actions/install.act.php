<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	
//--------------------------------------------------------------------------------------------------
//*	re/install a module
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and form vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('modulename', $_POST)) { $page->do404('module not specified'); }	

	//----------------------------------------------------------------------------------------------
	//	install the module
	//----------------------------------------------------------------------------------------------
	
	$report = '';

	if ('*' == $_POST['modulename']) {
		$mods = $kapenta->listModules();
		foreach($mods as $moduleName) {
			$model = new KModule($moduleName);
			if (true == $model->loaded) { 
				$report .= $model->install();		
			}
		}

	} else {
		$model = new KModule($_POST['modulename']);
		if (false == $model->loaded) { $page->do404('module not found: ' . $_POST['modulename']); }

		$report = $model->install();
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	
	$page->load('modules/admin/actions/install.page.php');
	$page->blockArgs['report'] = $report;
	$page->blockArgs['modulename'] = $model->modulename;
	$page->render();

?>
