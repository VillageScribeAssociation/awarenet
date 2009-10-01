<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for composing email
//--------------------------------------------------------------------------------------------------
function messages_composeform($args) {
	$html = loadBlock('modules/messages/views/composeform.block.php');
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>