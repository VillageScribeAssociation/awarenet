<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Package object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('savePackage' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Code_Package($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Package $UID");}

	if (false == $kapenta->user->authHas('code', 'Code_Package', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Package.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':		$model->name = $utils->cleanString($value); 		break;
			case 'description':	$model->description = $utils->cleanString($value); 	break;
			case 'version':		$model->version = $utils->cleanString($value); 		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Saved changes to Package', 'ok'); }
	else { $kapenta->session->msg('Could not save Package:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('code/showpackage/' . $model->alias); }

?>
