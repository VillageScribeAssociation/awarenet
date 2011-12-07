<?

//-------------------------------------------------------------------------------------------------
//|	add a WYSWYG editor to an HTML page (empty)
//-------------------------------------------------------------------------------------------------
//opt: name - name of html field, default is 'wyswyg' [string]
//opt: width - width of editor in pixels [string]
//opt: height - height of editor in pixels [string]
//opt: area - overrides areaname if present [string]

function editor_add($args) {
	global $page;

	$name = 'wyswyg';				//%	name of HTML form field [string]
	$width = 568;					//%	width of area, pixels [int]
	$height = 400;					//%	height of area, pixels [int]
	$areaname = 'area';				//%	name of HyperTextArea object [string]
	$path = '/modules/editor/';		//%	resource path [string]

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('name', $args)) { $name = $args['name']; }
	if (true == array_key_exists('width', $args)) { $width = $args['width']; }
	if (true == array_key_exists('height', $args)) { $height = $args['height']; }
	if (true == array_key_exists('area', $args)) { $args['areaname'] = $args['area']; }
	if (true == array_key_exists('areaname', $args)) { $areaname = $args['areaname']; }

	if (false == is_numeric($width)) { return ''; }
	if (false == is_numeric($height)) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------
	//$jsFile = '%%serverPath%%modules/editor/js/HyperTextArea.js';
	//$editorJs = "<script language='JavaScript' src='" . $jsFile . "'></script>\n";
	//$ss = '%%serverPath%%themes/%%defaultTheme/css/default.css';

	$html = ''
	 . "<div class='HyperTextArea' title='$name' width='$width' height='$height'></div>"
	 . "<script language='JavaScript' type='text/javascript'> khta.convertDivs(); </script>\n";	

	return $html;
}

//-------------------------------------------------------------------------------------------------

?>
