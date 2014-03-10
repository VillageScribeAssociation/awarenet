<?

//-------------------------------------------------------------------------------------------------
//|	add a WYSWYG editor to an HTML page and initialize with 
//-------------------------------------------------------------------------------------------------
//arg: jsvar - name of javascript variable which holds content to be edited [string]
//arg: name - name of html field [string]
//opt: width - width of editor in pixels [string]
//opt: height - height of editor in pixels [string]
//opt: areaname - name of javascript object [string]

function editor_base64($args) {
	global $kapenta;

	$width = 570;
	$height = 400;
	$areaname = 'area';

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('jsvar', $args)) { return '(no jsvar given)'; }
	if (false == array_key_exists('name', $args)) { return '(name not given)'; }
	if (true == array_key_exists('width', $args)) { $width = $args['width']; }
	if (true == array_key_exists('height', $args)) { $height = $args['height']; }
	if (true == array_key_exists('areaname', $args)) { $areaname = $args['areaname']; } 

	if (false == is_numeric($width)) { return ''; }
	if (false == is_numeric($height)) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------
	//TODO: ensure that page template includes /js/HyperTextArea.js

	$html = ''
		  . "<script language='JavaScript' type='text/javascript'>\n"
		  . $args['jsvar'] . " = kutils.base64_decode(" . $args['jsvar'] . ");\n"
		  . $areaname . " = new HyperTextArea('" . $args['name'] . "', " . $args['jsvar'] . ","
		  . " $width, $height,'/modules/editor/');\n"
		  . "</script>\n";	

	return $html;
}

//-------------------------------------------------------------------------------------------------
?>

