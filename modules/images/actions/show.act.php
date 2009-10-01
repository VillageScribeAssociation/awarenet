<?

//--------------------------------------------------------------------------------------------------
//	page to display a single image
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	
	$page->load($installPath . 'modules/images/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
