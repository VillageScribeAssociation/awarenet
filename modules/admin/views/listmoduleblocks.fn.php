<?


//--------------------------------------------------------------------------------------------------
//|	make a list of blocks on a given module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]

function admin_listmoduleblocks($args) {
		global $kapenta;
		global $user;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('module', $args)) { return '(module not given)'; }
	if (false == $kapenta->moduleExists($args['module'])) { return '(no such module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$blockList = $kapenta->listBlocks($args['module']);
	foreach($blockList as $block) {
		$block = str_replace('.block.php', '', $block);
		$editUrl = '%%serverPath%%admin/editblock/module_' . $args['module'] . '/' . $block;
		$html .= "\t\t<a href='" . $editUrl . "'>$block</a><br/>\n";
	}		

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

