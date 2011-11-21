<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Contact_Detail object and associate with an owner object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $_POST))
		{ $page->do404('reference module not specified', true); }
	if (false == array_key_exists('refModel', $_POST))
		{ $page->do404('reference model not specified', true); }
	if (false == array_key_exists('refUID', $_POST))
		{ $page->do404('reference object UID not specified', true); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];

	if (false == moduleExists($reModule)) { $page->do404('refModule does not exist', true); }
	if (false == $db->objectExists($refModel, $refUID)) { 
		$page->do404('specified owner does not exist in database', true); 
	}

	if (false == $user->authHas($refModule, $refModel, 'contact-add', $refUID))
		{ $page->do403('You are not authorized to create new Details.', true); }

	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Contact_Detail();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'refmodule':	$model->refModule = $utils->cleanString($value); 	break;
			case 'refmodel':	$model->refModel = $utils->cleanString($value); 	break;
			case 'refuid':		$model->refUID = $utils->cleanString($value); 		break;
			case 'type':		$model->type = $utils->cleanString($value); 		break;
			case 'description':	$model->description = $utils->cleanString($value); 	break;
			case 'value':		$model->value = $utils->cleanString($value); 		break;
			case 'isdefault':	$model->isDefault = $utils->cleanString($value); 	break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	$retUrl = 'contact'
		 . '/refModule_' . $model->refModule
		 . '/refModel_' . $model->refModel
		 . '/refUID_' . $model->refUID;

	if ('' == $report) {
		$session->msg("Added " . $model->type . "<br/>");
		$page->do302($retUrl);
	} else {
		$session->msg('Could not add contact detail:<br/>' . $report);
		$page->do302($retUrl);
	}



?>
