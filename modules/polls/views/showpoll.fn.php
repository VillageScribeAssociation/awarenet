<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display the poll for users to vote on / see results
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Poll_Question object [string]
//opt: questionUID - overrides UID if present [string]
//opt: width - widthof result bar chart [string]

function polls_showpoll($args) {
	global $kapenta;
	global $db;
	global $user;
	global $theme;	

	$width = 500;			//%	max width of bars in chart [int]
	$html = '';				//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('questionUID', $args)) { $args['UID'] = $args['questionUID']; }
	if (true == array_key_exists('width', $args)) { $width = (int)$args['width']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	$model = new Polls_Question($args['UID']);
	if (false == $model->loaded) { return '(could not load question)'; }

	if (true == $model->hasVoted($user->UID)) { 
		return "[[:polls::showpollresults::questionUID=" . $model->UID . "::width=$width:]]";
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array(array('', ''));
	$range = $model->loadAnswers();
	
	foreach($range as $item) {
		$radio = "<input type='radio' name='answer' value='" . $item['UID'] . "' />";
		$table[] = array($radio, $item['content']);
	} 

	$labels = array();
	$labels['questionUID'] = $model->UID;
	$labels['questionHtml'] = str_replace("\n", "<br/>", $model->content);	
	$labels['answerTable'] = $theme->arrayToHtmlTable($table, true, true);

	$block = $theme->loadBlock('modules/polls/views/showpoll.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;

}

?>
