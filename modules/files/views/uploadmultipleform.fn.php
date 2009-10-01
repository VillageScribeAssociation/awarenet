<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for uploading multiple files
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of files per page

function files_uploadmultipleform($args) {
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'files', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$labels = array('refModule' => $args['refModule'], 'refUID' => $args['refUID']);
	$html = replaceLabels($labels, loadBlock('modules/files/views/uploadmultiple.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>