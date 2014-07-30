<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show Contact_Details owned by some other object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $page->do404('refUID not given', true); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $page->do404('no refModel', true); }
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $page->do404('no refModule', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('no such refModule', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('no such owner', true); }

	if (false == $user->authHas($refModule, $refModel, 'contact-add', $refUID)) {
		$page->do403('You are not authorized to edit contact details.', true);
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/contact/actions/contact.if.page.php');
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->render();


?>
