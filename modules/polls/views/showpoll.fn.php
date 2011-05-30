<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display the poll for users to vote on / see results
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a Kapenta module [string]
//arg: refModel - type of object which will own poll question [string]
//arg: refUID - UID of object which will own poll question [string]

function polls_showpoll($args) {
	global $kapenta;
	global $db;
	global $user;
	global $theme;	

	$html = '';				//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	$model = new Polls_Question();
	$questionUID = $model->hasQuestion($refModule, $refModel, $refUID);
	if ('' == $questionUID) { return ''; }

	$model->load($questionUID);
	if (false == $model->loaded) { return '(could not load question)'; }

	if (true == $model->hasVoted($user->UID)) { 
		return "[[:polls::showpollresults::refModule=$refModule::refModel=$refModel::refUID=$refUID:]]";
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
