<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for uploading multiple files
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item which owns these files [string]

function files_uploadmultipleform($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;


	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return '(refModule not given)'; }
	if (array_key_exists('refModel', $args) == false) { return '(refModel not given)'; }
	if (array_key_exists('refUID', $args) == false) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	
	if (false == $kapenta->moduleExists($refModule)) { return '(no such ref module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no such ref object)'; }

	if (false == $user->authHas($refModule, $refModel, 'files-add', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$labels = array('refModule' => $args['refModule'], 'refUID' => $args['refUID']);
	$block = $theme->loadBlock('modules/files/views/uploadmultiple.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
