<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	add a minitaure gallery - like a slideshow, but with bigger thumbs, no next/prev
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item this owns images [string]

function images_minigal($args) {
		global $kapenta;
		global $user;
		global $kapenta;


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
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no owner object)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$src = $kapenta->serverPath . 'images/minigal'
		 . '/refModule_' . $refModule
		 . '/refModel_' . $refModel  
	     . '/refUID_' . $refUID . '/';
	     
	$html = "<iframe name='miniGalley" . $args['refUID'] . "' src='" . $src 
		. "' width='570' height='200' frameborder='no' ></iframe>\n";	
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

