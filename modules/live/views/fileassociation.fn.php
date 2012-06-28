<?

//--------------------------------------------------------------------------------------------------
//|	discover which module handles a file given its extension
//--------------------------------------------------------------------------------------------------
//:	this will return the name of a module or the empty string if none found
//arg: path - file anem or location [string]

function live_fileassociation($args) {
	global $registry;
	
	$path = '';						//%	file name or location [string]
	$module = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('path', $args)) { return ''; }
	$path = $args['path'];

	//----------------------------------------------------------------------------------------------
	//	look up extension in registry (may contain periods so don't use pathinfo)
	//----------------------------------------------------------------------------------------------
	$reg = $registry->search('live', 'live.file.');

	foreach($reg as $key => $value) {
		$ext = str_replace('live.file.', '', $key);
		$compare = substr($path, strlen($path) - strlen($ext));
		if (strtolower($ext) == strtolower($compare)) { $module = $value; }
	}

	return $module;
}

?>
