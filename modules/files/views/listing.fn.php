<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	add a list of files on a modules
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of object which owns these files [string]

function files_listing($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$src = '%%serverPath%%files/listing/refModule_' . $args['refModule'] 
	     . '/refUID_' . $args['refUID'] . '/';
	     
	$html = "<iframe name='listFiles" . $args['refUID'] . "' src='" . $src 
		. "' width='570' height='200' frameborder='no' ></iframe>\n";	
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

