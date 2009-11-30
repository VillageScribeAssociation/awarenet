<?

//-------------------------------------------------------------------------------------------------
//	list all files on this server which can be synced
//-------------------------------------------------------------------------------------------------
//	note: $request['ref'] should be a recent timestamp
//	note: auth is sha1('password' . 'timestamp')

	//---------------------------------------------------------------------------------------------
	//	check auth
	//---------------------------------------------------------------------------------------------

	$time = $request['ref'];
	$now = time();

	//if (is_numeric($time) == false) { doXmlError('invalid timestamp'); }
	//if (($time > ($now + 600)) || ($time < ($now - 600))) { doXmlError('invalid timestamp'); }

	//if (array_key_exists('auth', $request['args']) == fale) { doXmlError('not authorized'); }

	//$self = syncGetOwnData();
	//$auth = sha1($self['password'] . $time);
	//if ($request['args']['auth'] != $auth) { doXmlError('not authorized'); }

	//---------------------------------------------------------------------------------------------
	//	auth checks out, list the files
	//---------------------------------------------------------------------------------------------

	echo "#START\n";
	syncPrintFiles('data/images/');
	echo "#END";

	//---------------------------------------------------------------------------------------------
	//	utility function to recursively print all files
	//---------------------------------------------------------------------------------------------
	function syncPrintFiles($rootPath = UPLOAD_PATH_PROJECT, $iDepth = 0) {
		global $installPath;
		$iDepth++;
		if ($iDepth >= 10) { return false; }	// just in case
		$subDirs = array();

		//-----------------------------------------------------------------------------------------
		//	print all files in this dir
		//-----------------------------------------------------------------------------------------
		$files = scandir($installPath . $rootPath);
		foreach ($files as $file) {
			if (($file != '.') && ($file != '..') && ($file != '.svn')) { 
				if (is_dir($installPath . $rootPath . $file) == true) { $subDirs[] = $rootPath . $file . '/'; }
				else { echo $rootPath . $file . "\n"; }
			}
		}
	
		//-----------------------------------------------------------------------------------------
		//	recurse into all subdirectories
		//-----------------------------------------------------------------------------------------
		foreach ($subDirs as $subDir) {	syncPrintFiles($subDir, $iDepth); }
	} 

?>
