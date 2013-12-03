<?

//--------------------------------------------------------------------------------------------------
//|	form for editing a block template
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: block - name of a block [string]

function admin_editblockform($args) {
	global $page, $theme, $kapenta, $utils, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('module', $args)) { return '(module not given)'; }
	if (false == array_key_exists('block', $args)) { return '(block not given)'; }

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['refModuleName'] = $args['module'];
	$labels['refBlockName'] = $args['block'];
	$fileName = 'modules/'. $args['module'] .'/views/'. $args['block'] .'.block.php';

	if (false == $kapenta->fs->exists($fileName)) { return '(no such block)'; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = trim($theme->loadBlock($fileName)) . "\n";
	$block = str_replace($kapenta->serverPath, '%%serverPath%%', $block);
	$block = str_replace('http://kapenta.com', '%%serverPath%%', $block);		// legacy, remove
	$block = str_replace('http://kapenta.org.uk', '%%serverPath%%', $block);	// TODO: above
	$block = str_replace('http://awarenet.co.za', '%%serverPath%%', $block);
	$block = str_replace('http://awarenet.eu', '%%serverPath%%', $block);
	$block = str_replace('http://mothsorchid.com', '%%serverPath%%', $block);

	//----------------------------------------------------------------------------------------------
	//	make html/js edit form
	//----------------------------------------------------------------------------------------------
	$labels['blockContent'] = $block;
	$labels['blockContentJs64'] = $utils->base64EncodeJs('blockContentJs64', $block, false);
	$block = $theme->loadBlock('modules/admin/views/editblockform.block.php');	

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------
?>
