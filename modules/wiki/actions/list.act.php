<?

//--------------------------------------------------------------------------------------------------
//	list all pages on the wiki
//--------------------------------------------------------------------------------------------------

	//require_once($installPath . 'modules/wiki/models/wiki.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check for arguments
	//----------------------------------------------------------------------------------------------
	$pageno = 1; $num = 30;
	if (array_key_exists('page', $request['args']) != false) { $pageno = $request['args']['page']; }
	if (array_key_exists('num', $request['args']) != false) { $num = $request['args']['num']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/wiki/actions/list.page.php');
	$page->blockArgs['pageno'] = $pageno;
	$page->blockArgs['num'] = $num;
	$page->render();

?>
