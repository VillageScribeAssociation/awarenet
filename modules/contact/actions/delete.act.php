<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');	

//--------------------------------------------------------------------------------------------------
//*	delete a Contact_Detail object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('No contact detail specified', true); }

	$model = new Contact_Detail($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Contact detail not found.', true); }

	$authHas = $user->authHas($model->refModule, $model->refModel, 'contact-edit', $model->refUID);
	if (false == $authHas) { $page->do403('Not authorized', true); }

	//----------------------------------------------------------------------------------------------
	//	delete and redirect back to listing page
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$model->delete();

	//TODO: checks		
	$session->msg("Contact deleted.<br/>", 'ok');
	$page->do302($ext['ifUrl']);

?>
