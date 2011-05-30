<?

//--------------------------------------------------------------------------------------------------
//|	form for editing core setup
//--------------------------------------------------------------------------------------------------

function admin_coresetup($args) {
	global $registry, $user, $theme;
	$html = '';					//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check user is admin
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/coresetup.block.php');

	$labels = array(
		'kapenta.installpath' => $registry->get('kapenta.installpath'),
		'kapenta.serverpath' => $registry->get('kapenta.serverpath'),
		'kapenta.db.host' => $registry->get('kapenta.db.host'),
		'kapenta.db.user' => $registry->get('kapenta.db.user'),
		'kapenta.db.password' => $registry->get('kapenta.db.password'),
		'kapenta.db.name' => $registry->get('kapenta.db.name'),
		'kapenta.sitename' => $registry->get('kapenta.sitename'),
		'kapenta.themes.default' => $registry->get('kapenta.themes.default'),
		'kapenta.modules.default' => $registry->get('kapenta.modules.default')
	);

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
