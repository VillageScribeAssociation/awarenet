<?

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Question object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Polls_Question object [string]
//opt: questionUID - UID of a Polls_Question object, overrides UID [string]

function polls_editquestionform($args) {
	global $kapenta;
	global $theme;

	$raUID = '';				//%	UID of a Polls_Question object [string]
	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('questionUID', $args)) { $raUID = $args['questionUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Polls_Question($raUID);	//% the object we're editing [object:Polls_Question]

	if (false == $model->loaded) { return 'no such question: ' . $raUID; }
	if (false == $kapenta->user->authHas('polls', 'polls_question', 'edit', $model->UID)) { return 'authfail'; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/polls/views/editquestionform.block.php');
	$labels = $model->extArray();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

?>
