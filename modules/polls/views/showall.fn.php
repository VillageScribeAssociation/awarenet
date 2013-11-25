<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display multiple poll questions as a single form / result set
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a Kapenta module [string]
//arg: refModel - type of object which will own poll question [string]
//arg: refUID - UID of object which will own poll question [string]

function polls_showall($args) {
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

	//----------------------------------------------------------------------------------------------
	//	check whether this has any polls
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('polls_question', '*', $conditions, 'content ASC');

	if (0 == count($range)) { return ''; }

	foreach ($range as $item) {
		$model = new Polls_Question($item['UID']);

		if (false == $model->loaded) {
			$html .= "<div class='inlinequote'>(could not load question)</div>\n";
		} else {
			//--------------------------------------------------------------------------------------
			// question loaded
			//--------------------------------------------------------------------------------------
			
			if (true == $model->hasVoted($user->UID)) { 
				//----------------------------------------------------------------------------------
				//	show reult set
				//----------------------------------------------------------------------------------

				$html .= ''
				 . "[[:polls::showpollresults::questionUID=" . $item['UID'] . ":]]";

			} else {

				//----------------------------------------------------------------------------------
				//	shwo question form
				//----------------------------------------------------------------------------------
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

				$block = $theme->loadBlock('modules/polls/views/showall.block.php');
				$html .= $theme->replaceLabels($labels, $block);

			} // end if has voted
		} // end if loaded
	} // end foreach

	$html = $theme->ntb($html, 'Poll', 'divAllPolls' . $refUID);

	return $html;

}

?>
