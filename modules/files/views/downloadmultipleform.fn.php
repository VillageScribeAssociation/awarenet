<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for downloading multiple files
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - number of files per page [string]

function files_downloadmultipleform($args) {
		global $theme;
		global $kapenta;
		global $db;
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
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such ref object)'; }

	if (false == $user->authHas($refModule, $refModel, 'files-add', $refUID)) { return '(noauth)'; }

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------
	$labels = array(
		'refModule' => $refModule, 
		'refModel' => $refModel, 
		'refUID' => $refUID
	);

	$block = $theme->loadBlock('modules/files/views/downloadmultiple.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
