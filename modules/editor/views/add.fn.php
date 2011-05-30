<?

//-------------------------------------------------------------------------------------------------
//|	add a WYSWYG editor to an HTML page (empty)
//-------------------------------------------------------------------------------------------------
//arg: name - name of html field [string]
//opt: width - width of editor in pixels [string]
//opt: height - height of editor in pixels [string]

function editor_add($args) {
	global $page;
	$width = 568;
	$height = 400;

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('name', $args)) { return '(field name not given)'; }
	if (true == array_key_exists('width', $args)) { $width = $args['width']; }
	if (true == array_key_exists('height', $args)) { $height = $args['height']; }

	if (false == is_numeric($width)) { return ''; }
	if (false == is_numeric($height)) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------
	$jsFile = '%%serverPath%%modules/editor/HyperTextArea.js';
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

