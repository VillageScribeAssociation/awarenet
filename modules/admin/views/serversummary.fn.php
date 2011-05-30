<?

//--------------------------------------------------------------------------------------------------
//|	display basic info on this node
//--------------------------------------------------------------------------------------------------

function admin_serversummary($args) {
	global $kapenta, $registry, $user, $db, $theme;
	$html = '';					//%	return value [string:html]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array(
		array('Key', 'Setting'),
		array('<b>iPath:</b>', $registry->get('kapenta.installpath')),
		array('<b>sPath:</b>', $registry->get('kapenta.serverpath')),
		array('<b>date:</b>', $db->datetime())
	);

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
