<?

//--------------------------------------------------------------------------------------------------
//*	iframe console to allow user editing of tags and tag indexes
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check references and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('no refModule', true); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('no refModel', true); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('no refUID', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('No such module.', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->do404('No such object.', true); }

	if (
		(false == $kapenta->user->authHas($refModule, $refModel, 'tags-add', $refUID)) &&
		(false == $kapenta->user->authHas($refModule, $refModel, 'tags-manage', $refUID))
	) {
		echo "$refModule::$refModel::$refUID<br/>";
		$kapenta->page->do403("You dont have permissions to edit tags on this item.", true);
	}

	//----------------------------------------------------------------------------------------------
	//	show the iframe
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/tags/actions/edittags.if.page.php');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->render();

?>
