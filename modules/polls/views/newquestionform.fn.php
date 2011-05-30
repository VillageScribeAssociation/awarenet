<?

//--------------------------------------------------------------------------------------------------
//|	form to create a new Polls_Question object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a Kapenta module [string]
//arg: refModel - type of object which will own poll question [string]
//arg: refUID - UID of object which will own poll question [string]

function polls_newquestionform($args)  {
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

	if (false == $kapenta->moduleExists($args['refModule'])) { return '(no such module)'; }
	if (false == $db->objectExists($args['refModel'], $args['refUID'])) { return '(not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/polls/views/newquestionform.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}

?>
