<?

	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Notice object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveNotice' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Newsletter_Notice($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Notice $UID");}

	if (false == $user->authHas('newsletter', 'Newsletter_Notice', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Notice.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'edition':		$model->edition = $utils->cleanString($value); 	break;
			case 'title':		$model->title = $utils->cleanString($value); 	break;
			case 'content':		$model->content = $utils->cleanHtml($value); 	break;
			case 'category':	$model->category = $utils->cleanString($value); break;
			case 'shared':		$model->shared = $utils->cleanString($value); 	break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Notice', 'ok'); }
	else { $session->msg('Could not save Notice:<br/>' . $report, 'bad'); }

	//if (true == array_key_exists('return', $_POST)) {

		echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

		echo ''
		 . "<script>\n"
		 . "	var UID = window.name.replace('ifc', '');\n"
		 . "	if ((window.parent) && (window.parent.kwindowmanager)) {\n"
		 . "		var kwm = window.parent.kwindowmanager;\n"
		 . "		var hWnd = kwm.getIndex(UID);\n"
		 . "		window.parent.newsletter_reloadnotice('" . $model->UID . "');\n"
		 . "		window.parent.kwindowmanager.closeWindow(UID);\n"
		 . "	}\n"
		 . "</script>\n";

		echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	//} else { $kapenta->page->do302('newsletter/editnotice/' . $model->UID); }

?>
