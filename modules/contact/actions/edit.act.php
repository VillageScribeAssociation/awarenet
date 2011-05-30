<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to edit a contact detail (in iframe)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('no reference given', true); }

	$model = new Contact_Detail($req->ref);
	if (false == $model->loaded) { $page->do404('contact detail not found', true); }

	$authHas = $user->authHas($model->refModule, $model->refModel, 'contacts-edit', $model->refUID);
	if (false == $authHas) { $page->do403('403 - not authorised', true); }

	//----------------------------------------------------------------------------------------------
	//	make the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/contact/actions/edit.if.page.php');
	$page->blockArgs['refModule'] = $model->refModule;
	$page->blockArgs['refModel'] = $model->refModel;
	$page->blockArgs['refUID'] = $model->refUID;
	$page->blockArgs['UID'] = $model->UID;
	$page->render();

?>
