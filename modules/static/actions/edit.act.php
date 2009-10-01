<?

//--------------------------------------------------------------------------------------------------------------
//	edit a static page
//--------------------------------------------------------------------------------------------------------------

	if (authHas('static', 'edit', '') == false) { do403(); }
	if ($request['ref'] == '') { do302('static/list/'); }
	
	require_once($installPath . 'modules/static/models/static.mod.php');
	
	$page->load($installPath . 'modules/static/actions/edit.page.php');
	$page->blockArgs['UID'] = raGetOwner($request['ref'], 'static');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();
	
?>