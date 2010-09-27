<?

//--------------------------------------------------------------------------------------------------
//|	display admin controls for all modules which provide them
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this
//	NOTE: modules may implement an 'adminconsole' block, but it's not compulsory

function admin_moduleconsole($args) {
	global $kapenta, $user, $theme;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$mods = $kapenta->listModules();
	foreach ($mods as $moduleName) {
		$block = $theme->expandBlocks("[[:" . $moduleName . "::adminconsole:]]", '');
		if ($block != '') {	$html .= "<h2>$moduleName</h2>\n$block\n<hr/>\n"; }
	}

	return $html;
}

?>

