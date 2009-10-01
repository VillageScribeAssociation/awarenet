<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	iframe to upload multiple files
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of files per page
// * $args['categories'] = comma delimited list of categories these pictures can belong to

function files_uploadmultiple($args) {
	global $serverPath; 
	
	//----------------------------------------------------------------------------------------------
	//	input validation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return '(no module)'; }
	if (array_key_exists('refUID', $args) == false) { return '(no UID)'; }
	$categories = '';
	
	//----------------------------------------------------------------------------------------------
	//	check user is authorised
	//----------------------------------------------------------------------------------------------
	if (authHas($args['refModule'], 'view', '') == false) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the iframe
	//----------------------------------------------------------------------------------------------
	$srcUrl = $serverPath . 'files/uploadmultiple/refModule_' . $args['refModule'] 
		. '/refUID_' . $args['refUID'] . '/';
		
	if (array_key_exists('categories', $args)) { $srcUrl .= '/cats_' . $args['categories']; }
	$html = "<iframe src='" . $srcUrl . "' name='filesMul" . $args['refModule'] . "'" 
		. " width='570' height='200' frameborder='0'></iframe>";
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>