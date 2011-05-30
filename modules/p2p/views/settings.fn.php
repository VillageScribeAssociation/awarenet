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
		'p2p.server.url' => $registry->get('p2p.server.url')
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
