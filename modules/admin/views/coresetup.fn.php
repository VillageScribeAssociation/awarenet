<?

//--------------------------------------------------------------------------------------------------
//|	form for editing core setup
//--------------------------------------------------------------------------------------------------

function admin_coresetup($args) {
	global $kapenta, $user, $theme;
	$html = '';					//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check user is admin
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/coresetup.block.php');

	$labels = array(
		'kapenta.installpath' => $kapenta->registry->get('kapenta.installpath'),
		'kapenta.serverpath' => $kapenta->registry->get('kapenta.serverpath'),
		'kapenta.db.host' => $kapenta->registry->get('kapenta.db.host'),
		'kapenta.db.user' => $kapenta->registry->get('kapenta.db.user'),
		'kapenta.db.password' => $kapenta->registry->get('kapenta.db.password'),
		'kapenta.db.name' => $kapenta->registry->get('kapenta.db.name'),
		'kapenta.db.persistent' => $kapenta->registry->get('kapenta.db.persistent'),
		'kapenta.sitename' => $kapenta->registry->get('kapenta.sitename'),
		'kapenta.themes.default' => $kapenta->registry->get('kapenta.themes.default'),
		'kapenta.modules.default' => $kapenta->registry->get('kapenta.modules.default'),
		'kapenta.alternate' => $kapenta->registry->get('kapenta.alternate'),
		'kapenta.snstart' => $kapenta->registry->get('kapenta.snstart'),
		'kapenta.snend' => $kapenta->registry->get('kapenta.snend')
	);

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
