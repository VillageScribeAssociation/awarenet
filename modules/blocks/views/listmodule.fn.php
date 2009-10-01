<?


//--------------------------------------------------------------------------------------------------
//	make a list of blocks on a given module
//--------------------------------------------------------------------------------------------------

function blocks_listmodule($args) {
	global $serverPath;
	$html = '';

	if (array_key_exists('module', $args)) {
		$blockList = listBlocks($args['module']);
		foreach($blockList as $block) {
			$editUrl = $serverPath . 'blocks/edit/module_' . $args['module'] . '/' . $block;
			$html .= "\t\t<a href='" . $editUrl . "'>$block</a><br/>\n";
		}		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>