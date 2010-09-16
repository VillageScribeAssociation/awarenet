<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for composing email
//--------------------------------------------------------------------------------------------------
function messages_composeform($args) {
	global $theme;

	$html = $theme->loadBlock('modules/messages/views/composeform.block.php');
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>