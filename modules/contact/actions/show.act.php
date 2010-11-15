<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show Contact_Details owned by some other object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('refUID not given', true); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('no refModel', true); }
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('no refModule', true); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('no such refModule', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('no such owner', true); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/contact/actions/contact.if.page.php');
	$page->blockArgs['refUID'] = $refUID;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refModule'] = $refModule;
	$page->render();


?>
