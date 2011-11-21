<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new contact detail to some object
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

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('No such module.'); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('No such object.'); }

	if (false == $user->authHas($refModule, $refModel, 'contact-add', $refUID)) 
		{ $page->do403('Not authorized to add contact details.', true); }

	//----------------------------------------------------------------------------------------------
	//	all OK, make the new obejct
	//----------------------------------------------------------------------------------------------
	$model = new Contact_Detail();
	$model->refModule = $refModule;
	$model->refModel = $refModel;
	$model->refUID = $refUID;

	$model->description = 'new contact';
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect to edit form
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $page->do302('contact/edit/' . $model->UID); }
	else { $page->do404('could not create new contact detail:' . $report); }

?>
