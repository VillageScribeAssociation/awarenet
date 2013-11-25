<?

	require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Adunit object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveAdunit' != $_POST['action']) { $page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed.'); }

	$model = new Newsletter_Adunit($_POST['UID']);
	if (false == $model->loaded) { $page->do404("could not load Adunit $UID");}

	if (false == $user->authHas('newsletter', 'Newsletter_Adunit', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this Adunit.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanString($value); break;
			case 'tagline':		$model->tagline = $utils->cleanHtml($value); break;
			case 'linktext':	$model->linktext = $utils->cleanString($value); break;
			case 'linkurl':		$model->linkurl = $utils->cleanString($value); break;
			case 'pinned':		$model->pinned = $utils->cleanString($value); break;
			case 'weight':		$model->weight = $utils->cleanString($value); break;
			case 'shared':		$model->shared = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Adunit', 'ok'); }
	else { $session->msg('Could not save Adunit:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('newsletter/showadunit/' . $model->UID); }

?>
