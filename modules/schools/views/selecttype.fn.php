<?

//--------------------------------------------------------------------------------------------------
//|	create an HTML select box for choosing a school's type (high, middle, comprehensive, etc)
//--------------------------------------------------------------------------------------------------
//opt: default - default school type [string]
//opt: varname - name of HTML for field [string]

function schools_selecttype($args) {
		global $user;
		global $theme;

	$html = '';				//%	return type [string]	
	$default = '';			//%	default option [string]
	$varname = 'type';		//%	form field name [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize these
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/schools/views/schooltype.block.php');
	$labels = array('varname' => $varname, 'default' => $default);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
