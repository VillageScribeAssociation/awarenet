<?

//--------------------------------------------------------------------------------------------------
//*	perform module maintenance (check for dead references, ect)
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may do this

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }	// only admins may perform these actions

	if ('' == $req->ref) {
		//------------------------------------------------------------------------------------------
		//	no specific module referenced, show list of maintenance scripts
		//------------------------------------------------------------------------------------------
		$page->load('modules/admin/actions/maintenance.page.php');
		$page->render();

	} else {
		//------------------------------------------------------------------------------------------
		//	module referenced, run maintenance script and display report
		//------------------------------------------------------------------------------------------

		$mods = $kapenta->listModules();
		if (false == in_array($req->ref, $mods)) { $page->do404(); }	// no such module

		$page->load('modules/admin/actions/runmaintenance.page.php');
		$page->blockArgs['modName'] = $req->ref;
		$page->render();

	}
	

?>
