<?

//--------------------------------------------------------------------------------------------------
//|	select box for code object 'type'
//--------------------------------------------------------------------------------------------------
//opr: default - default type, optional [string]

function code_selecttype($args) {
	global $theme;

	$html = '';							//%	return value [string:html]
	$default = 'txt';					//%	default file type [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array('default' => $default);

	$block = $theme->loadBlock('modules/code/views/selecttype.block.php');
	$theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
