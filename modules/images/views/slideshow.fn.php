<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	add a slideshow
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object which may own images [string]
//arg: refUID - UID of object which owns images [string]

function images_slideshow($args) {
	global $kapenta, $db, $user;

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such owner)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$src = $kapenta->serverPath . 'images/slideshow'
		 . '/refModule_' . $refModule 
		 . '/refModel_' . $refModel 
	     . '/refUID_' . $refUID . '/';
	     
	$html = "<iframe name='slideShow" . $args['refUID'] . "' src='" . $src 
		. "' width='570' height='200' frameborder='no' ></iframe>\n";	
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
