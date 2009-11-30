<?

//--------------------------------------------------------------------------------------------------
//	moblog, all posts of from all blogs ordered by date
//--------------------------------------------------------------------------------------------------
	
	if (authHas('moblog', 'view', '') == false) { do403(''); }

	$pageNo = 1; // if not specified
	if (array_key_exists('page', $request['args']) == true) 
		{ $pageNo = floor($request['args']['page']); }

	$page->load($installPath . 'modules/moblog/actions/moblog.page.php');
	$page->blockArgs['pageno'] = $pageNo;
	$page->render();

?>
