<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display the poll for users to vote on / see results
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Polls_Question object [string]
//opt: questionUID - overrides UID of present [string]
//opt: width - width of result table, pixels (int) [string]
//opt: title - optional navtitlebox caption [string]

function polls_showpollresults($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $theme;

	$html = '';				//%	return value [string:html]
	$width = 500;

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('questionUID', $args)) { $args['UID'] = $args['questionUID']; }
	if (false == array_key_exists('UID', $args)) { return 'UID not given'; }
	if (true == array_key_exists('width', $args)) { $width = (int)$args['width']; }

	$model = new Polls_Question($args['UID']);
	if (false == $model->loaded) { return '(could not load question)'; }

	if (true == array_key_exists('title', $args)) {
		$html .= "[[:theme::navtitlebox::label=" .$args['title'] . ":]]\n";
	}

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
