<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	add a slideshow
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of images per page

function images_slideshow($args) {
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
	$src = $serverPath . 'images/slideshow/refModule_' . $args['refModule'] 
	     . '/refUID_' . $args['refUID'] . '/';
	     
	$html = "<iframe name='slideShow" . $args['refUID'] . "' src='" . $src 
		. "' width='570' height='200' frameborder='no' ></iframe>\n";	
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>