<?

//--------------------------------------------------------------------------------------------------
//|	form for editing p2p module settings
//--------------------------------------------------------------------------------------------------

function p2p_settings($args) {
	global $theme;
	global $kapenta;
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/p2p/views/settings.block.php');

	$labels = array(
		'p2p.enabled' => $kapenta->registry->get('p2p.enabled'),
		'p2p.server.uid' => $kapenta->registry->get('p2p.server.uid'),
		'p2p.server.name' => $kapenta->registry->get('p2p.server.name'),
		'p2p.server.url' => $kapenta->registry->get('p2p.server.url'),
		'p2p.server.fw' => $kapenta->registry->get('p2p.server.fw'),
		'p2p.server.pubkey' => $kapenta->registry->get('p2p.server.pubkey'),
		'p2p.batchsize' => $kapenta->registry->get('p2p.batchsize'),
		'p2p.batchparts' => $kapenta->registry->get('p2p.batchparts'),
		'p2p.filehours' => $kapenta->registry->get('p2p.filehours')
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
