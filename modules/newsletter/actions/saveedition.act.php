<?

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Edition object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveEdition' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Newsletter_Edition($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Edition $UID");}

	if (false == $kapenta->user->authHas('newsletter', 'Newsletter_Edition', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Edition.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'subject':		$model->subject = $utils->cleanString($value); break;
			case 'status':		$model->status = $utils->cleanString($value); break;
			case 'publishdate':	$model->publishdate = $utils->cleanString($value); break;
			case 'sentto':		$model->sentto = $utils->cleanString($value); break;
			case 'abstract':	$model->abstract = $utils->cleanHtml($value); break;
			case 'shared':		$model->shared = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Saved changes to Edition', 'ok'); }
	else { $kapenta->session->msg('Could not save Edition:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('newsletter/showedition/' . $model->alias); }

?>
