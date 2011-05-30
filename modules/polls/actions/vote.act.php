<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/answer.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/vote.mod.php');

//--------------------------------------------------------------------------------------------------
//*	vote on a poll question
//--------------------------------------------------------------------------------------------------
//postarg: question - UID fo a Polls_Question object [string]
//postarg: answer - UID fo a Polls_Answer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('question', $_POST)) { $page->do404('Question not specified'); }
	if (false == array_key_exists('answer', $_POST)) { $page->do404('Answer not specified'); }

	$question = new Polls_Question($_POST['question']);
	if (false == $question->loaded) { $page->do404('Question not found.'); }

	$answer = new Polls_Answer($_POST['answer']);
	if (false == $answer->loaded) { $page->do404('Unkown Answer.'); }

	$refModule = $question->refModule;
	$refModel = $question->refModel;
	$refUID = $question->refUID;

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check if user has already voted
	//----------------------------------------------------------------------------------------------
	$returnUrlBlock = "[[:$refModule::lookup::model=$refModel::UID=$refUID::link=no:]]";
	$returnUrl = $theme->expandBlocks($returnUrlBlock, '');
	$returnUrl = str_replace($kapenta->serverPath, '', $returnUrl);
	$returnUrl = str_replace('%%serverPath%%', '', $returnUrl);

	if (true == $question->hasVoted($user->UID)) {
		$session->msg("You have already voted in this poll.", 'bad');
		$page->do302($returnUrl);
	}

	//----------------------------------------------------------------------------------------------
	//	create the vote
	//----------------------------------------------------------------------------------------------
	$vote = new Polls_Vote();
	$vote->question = $question->UID;
	$vote->answer = $answer->UID;
	$report = $vote->save();

	if ('' == $report) {
		$session->msg('Thank you for voting.', 'ok');
	} else {
		$session->msg('Your vote could not be saved:<br/>' . $report, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to owner
	//----------------------------------------------------------------------------------------------
	$page->do302($returnUrl);

?>
