<?

//--------------------------------------------------------------------------------------------------
//|	form for editing p2p module settings
//--------------------------------------------------------------------------------------------------

function p2p_settings($args) {
	global $theme;
	global $registry;
	global $user;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/p2p/views/settings.block.php');

	$labels = array(
		'p2p.enabled' => $registry->get('p2p.enabled'),
		'p2p.server.uid' => $registry->get('p2p.server.uid'),
		'p2p.server.name' => $registry->get('p2p.server.name'),
		'p2p.server.url' => $registry->get('p2p.server.url'),
		'p2p.server.fw' => $registry->get('p2p.server.fw'),
		'p2p.server.pubkey' => $registry->get('p2p.server.pubkey'),
		'p2p.batchsize' => $registry->get('p2p.batchsize'),
		'p2p.batchparts' => $registry->get('p2p.batchparts'),
		'p2p.filehours' => $registry->get('p2p.filehours')
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
