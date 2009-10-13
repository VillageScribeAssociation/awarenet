<?

//-------------------------------------------------------------------------------------------------
//	add a WYSWYG editor to an HTML page (empty)
//-------------------------------------------------------------------------------------------------
//arg: name - name of html field
//opt: width - width of editor in pixels
//opt: height - height of editor in pixels

function editor_add($args) {
	global $page;
	global $serverPath;
	$width = 568;
	$height = 400;

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('name', $args) == false) { return ''; }
	if (array_key_exists('width', $args) == true) { $width = $args['width']; }
	if (array_key_exists('height', $args) == true) { $height = $args['height']; }

	if (is_numeric($width) == false) { return ''; }
	if (is_numeric($height) == false) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------
	$jsFile = $serverPath . '/modules/editor/HyperTextArea.js';
	$editorJs = "<script language='JavaScript' src='" . $jsFile . "'></script>\n";

	$html = $editorJs
		  . "<script language='JavaScript' type='text/javascript'>\n"
		  . "area = new HyperTextArea('" . $args['name'] . "', '',"
		  . " $width, $height,'/modules/editor/');\n"
		  . "</script>\n";	

	return $html;
}

//-------------------------------------------------------------------------------------------------
?>
