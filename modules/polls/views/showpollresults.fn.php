<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display the poll for users to vote on / see results
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a Kapenta module [string]
//arg: refModel - type of object which will own poll question [string]
//arg: refUID - UID of object which will own poll question [string]

function polls_showpollresults($args) {
	global $kapenta;
	global $db;
	global $user;
	global $theme;	

	$html = '';				//%	return value [string:html]
	$width = 500;

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }
	if (true == array_key_exists('width', $args)) { $width = (int)$args['wdith']; }

	$model = new Polls_Question();
	$questionUID = $model->hasQuestion($args['refModule'], $args['refModel'], $args['refUID']);
	if ('' == $questionUID) { return ''; }

	$model->load($questionUID);
	if (false == $model->loaded) { return '(could not load question)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/polls/views/showpollresults.block.php');
	$table = array(array('', ''));
	$answers = $model->loadAnswers();
	$results = $model->loadResults();
	
	$totalVotes = $model->getTotalVotes();
	$maxVotes = $model->getMaxVotes();
	if (0 == $totalVotes) { return '(no votes yet cast)'; }

	$imgUrl = '%%serverPath%%themes/%%defaultTheme%%/images/poll.png';

	foreach($answers as $answer) {
		$score = $results[$answer['UID']];
		$pc = floor(($score / $totalVotes) * 100);
		$wide = floor(($score / $totalVotes) * $width);

		$row = "<img src='$imgUrl' height='16' width='$wide' /><br/>"
			 . "<small><b>$score votes / " . $pc . "%</b></small><br/>"
			 . $answer['content'];

		$table[] = array($answer['weight'], $row);
	} 

	$labels = array();
	$labels['questionUID'] = $model->UID;
	$labels['questionHtml'] = str_replace("\n", "<br/>", $model->content);	
	$labels['answerTable'] = $theme->arrayToHtmlTable($table, true, true);
	$labels['totalVotes'] = $totalVotes;

	$html = $theme->replaceLabels($labels, $block);

	return $html;

}

?>
