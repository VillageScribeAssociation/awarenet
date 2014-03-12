<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates an abuse report iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->do403('Please log in to use this feature.', true); }	

	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('no refModule', true); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('no refModel', true); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('no refUID', true); }

	$refUID = $kapenta->request->args['refUID'];
	$refModel = strtolower($kapenta->request->args['refModel']);
	$refModule = $kapenta->request->args['refModule'];

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/abuse/actions/abusewindow.page.php');
	$kapenta->page->requireJs($kapenta->serverPath . 'modules/editor/js/HyperTextArea.js');
	$kapenta->page->requireJs($kapenta->serverPath . 'modules/live/js/live.js');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->render();

?>
