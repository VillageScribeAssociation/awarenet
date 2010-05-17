<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for downloading multiple files
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - number of files per page [string]

function files_downloadmultipleform($args) {
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
	$html = replaceLabels($labels, loadBlock('modules/files/views/downloadmultiple.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

