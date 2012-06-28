<?

//--------------------------------------------------------------------------------------------------
//|	settigns form for the chat module
//--------------------------------------------------------------------------------------------------

function chat_settings($args) {
	global $theme;
	global $user;
	global $registry;

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
		'chat.enabled' => $registry->get('chat.enabled'),
		'chat.server' => $registry->get('chat.server'),
		'p2p.server.uid' => $registry->get('p2p.server.uid'),
		'p2p.server.url' => $registry->get('p2p.server.url'),
		'p2p.server.name' => $registry->get('p2p.server.name')
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
