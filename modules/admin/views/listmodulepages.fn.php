<?

//--------------------------------------------------------------------------------------------------
//|	list all pages on a module
//--------------------------------------------------------------------------------------------------

function admin_listmodulepages($args) {
		global $user;
		global $kapenta;

	$html = '';													//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('module', $args)) { return '(module not speicified)'; }
	if (false == $kapenta->moduleExists($args['module'])) { return '(no such module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$pageList = $kapenta->listPages($args['module']);
	foreach($pageList as $pg) {
		$editUrl = '%%serverPath%%admin/editpage/module_' . $args['module'] . '/' . $pg;
		$html .= "\t\t<a href='" . $editUrl . "'>$pg</a><br/>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
