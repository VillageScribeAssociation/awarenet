<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	iframe to upload multiple files
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object which will own files [string]
//arg: refUID - UID of object which will own files [string]
//opt: categories - comma delimited list of categories these files can belong to [string]

function files_uploadmultiple($args) {
		global $kapenta;
		global $kapenta;

	
	//----------------------------------------------------------------------------------------------
	//	input validation
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	$categories = '';
	
	if (false == $kapenta->user->authHas($args['refModule'], $args['refModel'], 'files-add', $args['refUID']))
		{ return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the iframe
	//----------------------------------------------------------------------------------------------
	$srcUrl = '%%serverPath%%files/uploadmultiple'
		 . '/refModule_' . $args['refModule']
		 . '/refModel_' . $args['refModel']  
		 . '/refUID_' . $args['refUID'] . '/';
		
	if (array_key_exists('categories', $args)) { $srcUrl .= '/cats_' . $args['categories']; }
	$html = "<iframe src='" . $srcUrl . "' name='filesMul" . $args['refModule'] . "'" 
		. " width='570' height='200' frameborder='0'></iframe>";
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

