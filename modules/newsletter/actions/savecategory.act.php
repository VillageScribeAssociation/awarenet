<?

	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Category object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveCategory' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Newsletter_Category($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Category $UID");}

	if (false == $kapenta->user->authHas('newsletter', 'Newsletter_Category', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Category.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':	$model->name = $utils->cleanString($value); break;
			case 'description':	$model->description = $utils->cleanString($value); break;
			case 'weight':	$model->weight = $utils->cleanString($value); break;
			case 'shared':	$model->shared = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Saved changes to Category', 'ok'); }
	else { $kapenta->session->msg('Could not save Category:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { 
		//------------------------------------------------------------------------------------------
		//	close js window
		//------------------------------------------------------------------------------------------

		echo ''
		 . "<script>\n"
		 . "	var UID = window.name.replace('ifc', '');\n"
		 . "	if ((window.parent) && (window.parent.kwindowmanager)) {\n"
		 . "		var kwm = window.parent.kwindowmanager;\n"
		 . "		var hWnd = kwm.getIndex(UID);\n"
		 . "		window.parent.newsletter_reloadcategories();\n"
		 . "		window.parent.kwindowmanager.closeWindow(UID);\n"
		 . "	}\n"
		 . "</script>\n";

	}

?>
