<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/answer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	record a new answer for a poll question and redirect back to the edit frame
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not given', true); }
	if ('addAnswer' != $_POST['action']) { $kapenta->page->do404('action not recognized', true); }
	if (false == array_key_exists('question', $_POST)) { $kapenta->page->do404('no question', true); }
	if (false == array_key_exists('answer', $_POST)) { $kapenta->page->do404('no answer', true); }

	$question = new Polls_Question($_POST['question']);

	if (false == $question->loaded) { $kapenta->page->do404('question not given', true); }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	add the answer
	//----------------------------------------------------------------------------------------------
	$answer = new Polls_Answer();
	$answer->question = $question->UID;
	$answer->content = $_POST['answer'];			//TODO: santize this
	$answer->weight = $question->getMaxWeight() + 1;
	$report = $answer->save();

	if ('' == $report) {
		$kapenta->session->msg('Added option/answer.', 'ok');
	} else {
		$kapenta->session->msg('Could not add option/answer:<br/>' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to edit iframe
	//----------------------------------------------------------------------------------------------
	$url = 'polls/editquestion'
		. '/refModule_' . $question->refModule
		. '/refModel_' . $question->refModel
		. '/refUID_' . $question->refUID . '/';

	$kapenta->page->do302($url);
	

?>
