<?

//--------------------------------------------------------------------------------------------------
//	sync with awarenet working copy
//--------------------------------------------------------------------------------------------------
// TO USE: make list of install using find ./ > an-working.txt | an-install.txt and copy to this
// directory.  First step is to discover which files exist in the working (SVN) copy, but not in
// this one.

//--------------------------------------------------------------------------------------------------
//	load boath text files into arrays
//--------------------------------------------------------------------------------------------------

	$workingFile = $installPath . 'modules/uploader/an-working.txt';
	$installFile = $installPath . 'modules/uploader/an-install.txt';

	$working = array();
	$raw = implode(file($workingFile));
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$use = true;
		if (strpos($line, '/data/files/') != false) { $use = false; }	// no data
		if (strpos($line, '/data/images/') != false) { $use = false; }	// no data
		if (strpos($line, '/data/log/') != false) { $use = false; }		// no data
		if (strpos($line, '/.svn') != false) { $use = false; }			// no subversion data
		if ($use == true) { $working[] = $line; }
	}

	$install = array();
	$raw = implode(file($installFile));
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$use = true;
		if (strpos($line, '/uploader/') != false) { $use = false; }		// not this folder
		if (strpos($line, '/data/files/') != false) { $use = false; }	// no data
		if (strpos($line, '/data/images/') != false) { $use = false; }	// no data
		if (strpos($line, '/data/log/') != false) { $use = false; }		// no data
		if (strpos($line, '~') != false) { $use = false; }				// no tildefiles
		if (strpos($line, '/drawcache/') != false) { $use = false; }	// no cached graphics

		if ($use == true) { $install[] = $line; }
	}

//--------------------------------------------------------------------------------------------------
//	discover which files are not in the install (need to be deleted from svn)
//--------------------------------------------------------------------------------------------------

	foreach($working as $workingLine) {
		$found = false;

		foreach ($install as $installLine) {
			if ($installLine == $workingLine) { $found =  true; }
		}

		if ($found == false) {
			echo "svn delete " . str_replace('./', './trunk/', $workingLine) . "<br/>\n";
		}
	}

?>
