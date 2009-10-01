<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	add a minitaure gallery - like a slideshow, but with bigger thumbs, no next/prev
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of images per page

function images_minigal($args) {
	global $serverPath;

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$src = $serverPath . 'images/minigal/refModule_' . $args['refModule'] 
	     . '/refUID_' . $args['refUID'] . '/';
	     
	$html = "<iframe name='miniGalley" . $args['refUID'] . "' src='" . $src 
		. "' width='570' height='200' frameborder='no' ></iframe>\n";	
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>