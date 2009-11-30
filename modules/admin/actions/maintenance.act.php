<?

//-------------------------------------------------------------------------------------------------
//	perform module maintenance (check for dead references, ect)
//-------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }	// only admins may perform these actions

	if ($request['ref'] == '') {
		//-----------------------------------------------------------------------------------------
		//	no specific module referenced, show list of maintenance scripts
		//-----------------------------------------------------------------------------------------
		$page->load($installPath . 'modules/admin/actions/maintenance.page.php');
		$page->render();

	} else {
		//-----------------------------------------------------------------------------------------
		//	module referenced, run maintenance script and display report
		//-----------------------------------------------------------------------------------------

		$mods = listModules();
		if (in_array($request['ref'], $mods) == false) { do404(); }	// no such module

		$page->load($installPath . 'modules/admin/actions/runmaintenance.page.php');
		$page->blockArgs['modName'] = $request['ref'];
		$page->render();

	}
	

?>
