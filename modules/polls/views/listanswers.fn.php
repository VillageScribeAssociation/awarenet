<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all answers to a question, ordered by weight
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Polls_Question object [string]
//opt: questionUID - overrides UID if present [string]

function polls_listanswers($args) {
	global $db;
	global $theme;

	$html = '';			//%	return value;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('questionUID', $args)) { $args['UID'] = $args['questionUID']; }
	if (false == array_key_exists('UID', $args)) { return '(question not specified)'; }

	$model = new Polls_Question($args['UID']);
	if (false == $model->loaded) { return '(question not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('votes', 'question', 'weight', '[x]', '[x]');
	$range = $model->loadAnswers();
	foreach($range as $item) {
		$table[] = array('x', $item['content'], $item['weight'], '[del]', '[top]');
	}
	
	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
