<?

//--------------------------------------------------------------------------------------------------
//|	development /testing view to inject a message into a user's local chat inbox
//--------------------------------------------------------------------------------------------------

function chat_inject($args) {
	global $user;
	global $theme;	

	if ('admin' != $user->role) { return '(devlopment only)'; }

	$block = $theme->loadBlock("modules/chat/views/inject.block.php");
	$html = $theme->replaceLabels($args, $block);
	return $html;
}

?>
