<?

//--------------------------------------------------------------------------------------------------
//|	settigns form for the chat module
//--------------------------------------------------------------------------------------------------

function chat_settings($args) {
	global $theme;
	global $user;
	global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/chat/views/settings.block.php');

	$labels = array(
		'chat.enabled' => $kapenta->registry->get('chat.enabled'),
		'chat.server' => $kapenta->registry->get('chat.server'),
		'p2p.server.uid' => $kapenta->registry->get('p2p.server.uid'),
		'p2p.server.url' => $kapenta->registry->get('p2p.server.url'),
		'p2p.server.name' => $kapenta->registry->get('p2p.server.name')
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
