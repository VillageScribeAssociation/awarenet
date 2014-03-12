<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Question object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveQuestion' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Polls_Question($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Question.");}

	if (false == $kapenta->user->authHas('polls', 'Polls_Question', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Question.'); }
	if (false == array_key_exists('module', $_POST))
		{ $kapenta->page->do404('reference module not specified', true); }
	if (false == array_key_exists('model', $_POST))
		{ $kapenta->page->do404('reference model not specified', true); }
	if (false == array_key_exists('UID', $_POST))
		{ $kapenta->page->do404('reference object UID not specified', true); }
	if (false == moduleExists($_POST['module']))
		{ $kapenta->page->do404('specified module does not exist', true); }
	if (false == $kapenta->db->objectExists($_POST['model'], $_POST['UID']))
		{ $kapenta->page->do404('specified owner does not exist in database', true); }


	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'refmodule':	$model->refModule = $utils->cleanString($value); break;
			case 'refmodel':	$model->refModel = $utils->cleanString($value); break;
			case 'refuid':		$model->refUID = $utils->cleanString($value); break;
			case 'content':		$model->content = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Saved changes to Question', 'ok'); }
	else { $kapenta->session->msg('Could not save Question:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('polls/showquestion/' . $model->UID); }

?>
