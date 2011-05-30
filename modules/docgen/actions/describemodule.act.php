<?

//-------------------------------------------------------------------------------------------------
//*	describe a module
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check that the module exists
	//---------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	if (false == in_array($req->ref, $kapenta->listModules())) { $page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------
	$page->load('modules/docgen/actions/describemodule.page.php');
	$page->blockArgs['modname'] = $req->ref;
	$page->render();

?>
