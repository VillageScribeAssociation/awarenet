<?

//--------------------------------------------------------------------------------------------------
//*	list all pages on the wiki
//--------------------------------------------------------------------------------------------------

	//require_once($kapenta->installPath . 'modules/wiki/models/wiki.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check for arguments
	//----------------------------------------------------------------------------------------------
	$pageno = 1; $num = 30;
	if (array_key_exists('page', $kapenta->request->args) != false) { $pageno = $kapenta->request->args['page']; }
	if (array_key_exists('num', $kapenta->request->args) != false) { $num = $kapenta->request->args['num']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/wiki/actions/list.page.php');
	$kapenta->page->blockArgs['pageno'] = $pageno;
	$kapenta->page->blockArgs['num'] = $num;
	$kapenta->page->render();

?>
