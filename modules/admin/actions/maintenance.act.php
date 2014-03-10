<?

//--------------------------------------------------------------------------------------------------
//*	perform module maintenance (check for dead references, ect)
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may do this

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }	// only admins may perform these actions

	if ('' == $kapenta->request->ref) {
		//------------------------------------------------------------------------------------------
		//	no specific module referenced, show list of maintenance scripts
		//------------------------------------------------------------------------------------------
		$kapenta->page->load('modules/admin/actions/maintenance.page.php');
		$kapenta->page->render();

	} else {
		//------------------------------------------------------------------------------------------
		//	module referenced, run maintenance script and display report
		//------------------------------------------------------------------------------------------

		$mods = $kapenta->listModules();
		if (false == in_array($kapenta->request->ref, $mods)) { $kapenta->page->do404(); }	// no such module

		$kapenta->page->load('modules/admin/actions/runmaintenance.page.php');
		$kapenta->page->blockArgs['modName'] = $kapenta->request->ref;
		$kapenta->page->render();

	}
	

?>
