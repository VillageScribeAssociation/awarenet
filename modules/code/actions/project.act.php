<?

//--------------------------------------------------------------------------------------------------
//	show a code project
//--------------------------------------------------------------------------------------------------
	
	if ($kapenta->request->ref == '') { $page->do404(); }
	$owner = raGetOwner($kapenta->request->ref, 'codeprojects');
	if ($owner == false) { $page->do404(); }
	
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');

	$kapenta->page->load('modules/code/actions/project.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['UID'] = $owner;
	$kapenta->page->render();

?>
