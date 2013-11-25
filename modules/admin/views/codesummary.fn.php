<?

//--------------------------------------------------------------------------------------------------
//|	reads kapenta comments to get a file summary, if present
//--------------------------------------------------------------------------------------------------
//arg: path - file location relative to installPath [string]

function admin_codesummary($args) {
	global $user;
	global $kapenta;

	$firstFn = '';		//%	first function description found [string]
	$firstStar = '';	//%	first star comment [string]
	$summary = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('path', $args)) { return '(path not given)'; }	
	if (false == $kapenta->fs->exists($args['path'])) { return '(file not found)'; }

	//----------------------------------------------------------------------------------------------
	//	read the file
	//----------------------------------------------------------------------------------------------
	$raw = $kapenta->fs->get($args['path']);
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		if (('//*' == substr($line . 'xxx', 0, 3)) && ('' == $firstStar)) { 
			$firstStar = substr($line, 3);
		}
		if (('//|' == substr($line . 'xxx', 0, 3)) && ('' == $firstFn)) { 
			$firstFn = substr($line, 3);
		}
	}

	$summary = $firstStar;
	if ('' == $summary) { $summary = $firstFn; }

	return $summary;
}

?>
