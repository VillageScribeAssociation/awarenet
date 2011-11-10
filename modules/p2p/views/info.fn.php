<?

//--------------------------------------------------------------------------------------------------
//|	show public registration information
//--------------------------------------------------------------------------------------------------

function p2p_info($args) {
	global $registry;
	global $theme;

	$block = $theme->loadBlock('modules/p2p/views/info.block.php');
	$labels = array(
		'p2p.server.uid' => $registry->get('p2p.server.uid'),
		'p2p.server.name' => $registry->get('p2p.server.name'),
		'p2p.server.url' => $registry->get('p2p.server.url'),
		'p2p.server.pubkey' => $registry->get('p2p.server.pubkey')
	);

	$html = $theme->replaceLabels($labels, $block);		
	return $html;
}

?>
