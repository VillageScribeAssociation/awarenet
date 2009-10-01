<?


//--------------------------------------------------------------------------------------------------
//	form for editing a block
//--------------------------------------------------------------------------------------------------

function blocks_editform($args) {
	if ((array_key_exists('module', $args) AND (array_key_exists('block', $args)))) {
		$labels = array();
		$labels['moduleName'] = $args['module'];
		$labels['blockName'] = $args['block'];
		$fileName = 'modules/' . $args['module'] . '/' . $args['block'];
		$labels['blockContent'] = loadBlock($fileName);

		// sanitize content (prevent blocks running, </textarea>)
		$labels['blockContent']  = str_replace('[', '&#91;', $labels['blockContent']);			
		$labels['blockContent']  = str_replace(']', '&#93;', $labels['blockContent']);			
		$labels['blockContent']  = str_replace('<', '&lt;', $labels['blockContent']);			
		$labels['blockContent']  = str_replace('>', '&gt;', $labels['blockContent']);

		$block = loadBlock('modules/blocks/views/editform.block.php');	// load form
		return replaceLabels($labels, $block);
	}
}


?>