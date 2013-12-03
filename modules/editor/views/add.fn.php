<?

//-------------------------------------------------------------------------------------------------
//|	add a WYSWYG editor to an HTML page (empty)
//-------------------------------------------------------------------------------------------------
//opt: name - name of html field, default is 'wyswyg' [string]
//opt: width - width of editor in pixels [string]
//opt: height - height of editor in pixels [string]
//opt: area - overrides areaname if present [string]
//opt: refModule - module which owns this object [string]
//opt: refModel - type of object this editor is for [string]
//opt: refUID - UID of object this editor is for [string]

function editor_add($args) {
	global $page;
	global $kapenta;
	global $db;

	$name = 'wyswyg';				//%	name of HTML form field [string]
	$width = 568;					//%	width of area, pixels [int]
	$height = -1;					//%	height of area, pixels [int]
	$areaname = 'area';				//%	name of HyperTextArea object [string]
	$path = '/modules/editor/';		//%	resource path [string]
	$ref = '';

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

	if (
		(true == array_key_exists('refModule', $args)) &&
		(true == array_key_exists('refModel', $args)) &&
		(true == array_key_exists('refUID', $args))
	 ) {

		$refModule = $args['refModule'];
		$refModel = $args['refModel'];
		$refUID = $args['refUID'];

		if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
		if (false == $db->objectExists($refModel, $refUID)) {
			//return '(no such owner ' . $refModel . '::' . $refUID . ')';
		}

		$ref = "refModule='$refModule' refModel='$refModel' refUID='$refUID'";
	}

	//---------------------------------------------------------------------------------------------
	//	make the script block
	//---------------------------------------------------------------------------------------------

	$html = ''
	 . "<div"
	 . " class='HyperTextArea64'"
	 . " title='$name'"
	 . " style='visibility: hidden; display: none'"
	 . " width='$width'"
	 . " height='$height'"
	 . " karea='" . $areaname . "'"
	 . " $ref"
	 . "></div>"
	 . "<script language='JavaScript' type='text/javascript'> khta.convertDivs(); </script>\n";	

	//$html = "<textarea rows='10' style='width: 100%'>" . htmlentities($html) . "</textarea>";

	return $html;
}

//-------------------------------------------------------------------------------------------------

?>
