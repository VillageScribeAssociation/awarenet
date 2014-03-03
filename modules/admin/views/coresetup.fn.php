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
		'db.mysql.host' => $kapenta->registry->get('db.mysql.host'),
		'db.mysql.user' => $kapenta->registry->get('db.mysql.user'),
		'db.mysql.password' => $kapenta->registry->get('db.mysql.password'),
		'db.mysql.name' => $kapenta->registry->get('db.mysql.name'),
		'db.mysql.persistent' => $kapenta->registry->get('db.mysql.persistent'),
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
