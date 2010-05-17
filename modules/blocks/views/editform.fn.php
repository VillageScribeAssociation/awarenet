<?

//--------------------------------------------------------------------------------------------------
//|	form for editing a block template
//--------------------------------------------------------------------------------------------------

function blocks_editform($args) {
	global $serverPath;
	if ($user->data['ofGroup'] == 'admin') { do404(); }
	if ((array_key_exists('module', $args) AND (array_key_exists('block', $args)))) {
		//-----------------------------------------------------------------------------------------
		//	check arguments
		//-----------------------------------------------------------------------------------------
		$labels = array();
		$labels['refModuleName'] = $args['refmodule'];
		$labels['refBlockName'] = $args['refblock'];
		$fileName = 'modules/'. $args['refmodule'] .'/views/'. $args['refblock'] .'.block.php';

		if (file_exists($installPath . $fileName) == false) { return '(no such block)'; }

		//-----------------------------------------------------------------------------------------
		//	load the block
		//-----------------------------------------------------------------------------------------
		$block = trim(loadBlock($fileName)) . "\n";
		$block = str_replace($serverPath, '%%serverPath%%', $block);
		$block = str_replace('http://kapenta.com', '%%serverPath%%', $block);
		$block = str_replace('http://kapenta.org.uk', '%%serverPath%%', $block);
		$block = str_replace('http://awarenet.co.za', '%%serverPath%%', $block);
		$block = str_replace('http://awarenet.eu', '%%serverPath%%', $block);
		$block = str_replace('http://mothsorchid.com', '%%serverPath%%', $block);

		//-----------------------------------------------------------------------------------------
		//	make html/js edit form
		//-----------------------------------------------------------------------------------------
		$labels['blockContent'] = $block;
		$labels['blockContentJs64'] = base64EncodeJs('blockContentJs64', $block, false);
		$block = loadBlock('modules/blocks/views/editform.block.php');	
		return replaceLabels($labels, $block);
	}
}

//--------------------------------------------------------------------------------------------------
?>

