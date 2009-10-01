<?

//--------------------------------------------------------------------------------------------------
//	edit a school record
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'edit', '') == false) { do403(); }
	if ($request['ref'] == '') { do404(); }
	
	$page->load($installPath . 'modules/schools/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = raGetOwner($request['ref'], 'schools');
	$page->render();

?>
