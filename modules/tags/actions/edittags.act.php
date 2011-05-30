<?

//--------------------------------------------------------------------------------------------------
//*	iframe console to allow user editing of tags and tag indexes
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check references and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('no refModule', true); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('no refModel', true); }
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('no refUID', true); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('No such module.', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('No such object.', true); }

	if (false == $user->authHas($refModule, $refModel, 'tags-manage', $refUID)) 
		{ $page->do403("You dont have permissions to edit tags on this item.", true); }

	//----------------------------------------------------------------------------------------------
	//	show the iframe
	//----------------------------------------------------------------------------------------------
	$page->load('modules/tags/actions/edittags.if.page.php');
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refUID'] = $refUID;
	$page->render();

?>
