<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to edit a contact detail (in iframe)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('no reference given', true); }

	$model = new Contact_Detail($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('contact detail not found', true); }

	$authHas = $user->authHas($model->refModule, $model->refModel, 'contact-edit', $model->refUID);
	if (false == $authHas) { $page->do403('403 - not authorised', true); }

	//----------------------------------------------------------------------------------------------
	//	make the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/contact/actions/edit.if.page.php');
	$kapenta->page->blockArgs['refModule'] = $model->refModule;
	$kapenta->page->blockArgs['refModel'] = $model->refModel;
	$kapenta->page->blockArgs['refUID'] = $model->refUID;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->render();

?>
