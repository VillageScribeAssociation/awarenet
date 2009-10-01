<?

//--------------------------------------------------------------------------------------------------
//	page to display a single file
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	
	$page->load($installPath . 'modules/files/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
