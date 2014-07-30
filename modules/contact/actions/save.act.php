<?

	require_once($kapenta->installPath . 'modules/contact/models/detail.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a contact detail (if permissions allow) and redirect to listing iframe
//--------------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveDetail' != $_POST['action']) { $page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed.'); }

	$model = new Contact_Detail($_POST['UID']);
	if (false == $model->loaded) { $page->do404("could not load Detail $UID");}

	if (false == $user->authHas($model->refModule, $model->refModel, 'contact-edit', $model->refUID))
		{ $page->do403('You are not authorized to edit this Detail.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
		//	case 'refmodule':	$model->refModule = $utils->cleanString($value); break;
		//	case 'refmodel':	$model->refModel = $utils->cleanString($value); break;
		//	case 'refuid':	$model->refUID = $utils->cleanString($value); break;
			case 'type':	$model->type = $utils->cleanString($value); break;
			case 'description':	$model->description = $utils->cleanString($value); break;
			case 'value':	$model->value = $utils->cleanString($value); break;
			case 'isdefault':	$model->isDefault = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to contact', 'ok'); }
	else { $session->msg('Could not save contact:<br/>' . $report, 'bad'); }

	$retUrl = 'contact/show'
		 . '/refModule_' . $model->refModule
		 . '/refModel_' . $model->refModel
		 . '/refUID_' . $model->refUID . '/';

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302($retUrl); }

?>
