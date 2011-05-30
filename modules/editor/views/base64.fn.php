<?

//-------------------------------------------------------------------------------------------------
//|	add a WYSWYG editor to an HTML page and initialize with 
//-------------------------------------------------------------------------------------------------
//arg: jsvar - name of javascript variable which holds content to be edited [string]
//arg: name - name of html field [string]
//opt: width - width of editor in pixels [string]
//opt: height - height of editor in pixels [string]

function editor_base64($args) {
	global $page;
	$width = 570;
	$height = 400;

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('jsvar', $args) == false) { return ''; }
	if (array_key_exists('name', $args) == false) { return ''; }
	if (array_key_exists('width', $args) == true) { $width = $args['width']; }
	if (array_key_exists('height', $args) == true) { $height = $args['height']; }

	if (is_numeric($width) == false) { return ''; }
	if (is_numeric($height) == false) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------
	$jsFile = '%%serverPath%%/modules/editor/HyperTextArea.js';
	$editorJs = "<script language='JavaScript' src='" . $jsFile . "'></script>\n";

	$html = $editorJs
		  . "<script language='JavaScript' type='text/javascript'>\n"
		  . $args['jsvar'] . " = base64_decode(" . $args['jsvar'] . ");\n"
		  . "area = new HyperTextArea('" . $args['name'] . "', " . $args['jsvar'] . ","
		  . " $width, $height,'/modules/editor/');\n"
		  . "</script>\n";	

	return $html;
}

//-------------------------------------------------------------------------------------------------
?>

