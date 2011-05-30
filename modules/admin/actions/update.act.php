<?

	require_once($kapenta->installPath . 'modules/admin/models/repository.mod.php');

//-------------------------------------------------------------------------------------------------
//*	update local installation from repository on kapenta.org.uk
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	set up repository access
	//---------------------------------------------------------------------------------------------

	$projectUID = '210833480571475984';
	$repository = 'http://kapenta.org.uk/code/';

	$repository = new CodeRepository($repository, $projectUID, '');

	$repository->addExemption("setup.inc.php");				// dynamically generated on install
	$repository->addExemption("install.php");				// security risk
	$repository->addExemption("___install.php");			// security risk
	$repository->addExemption("ainstall.php");				// security risk
	$repository->addExemption("phpinfo.php");				// security risk
	$repository->addExemption("todo.txt");					// security risk
	$repository->addExemption("uploader/");					// this module (CONTAINS KEY)
	$repository->addExemption("install/");					// defunct
	$repository->addExemption(".svn");						// sybversion files and directories
	$repository->addExemption("some.txt");					// ?
	$repository->addExemption("GlobalFunctions.txt");		// ?
	//$repository->addExemption("data/log/");				// logs
	$repository->addExemption("/chan/");					// not part of awareNet
	$repository->addExemption('svnadd.sh');					// development SVN script
	$repository->addExemption('svndelete.sh');				// development SVN script
	$repository->addExemption('data/log/e');				// ?
	$repository->addExemption('.log.php');					// log files
	$repository->addExemption('~');							// gedit revision files
	$repository->addExemption('/drawcache/');				// dynamically generated images

	for ($i = 0; $i < 10; $i++) {
		$repository->addExemption("data/images/" . $i);		// user images
		$repository->addExemption("data/files/" . $i);		// user files
	}

	//---------------------------------------------------------------------------------------------
	//	get list of files from respoitory
	//---------------------------------------------------------------------------------------------

	echo "[i] getting repository list: " . $repository->listUrl . "... "; flush();
	$rList = $repository->getRepositoryList();
	echo "done <br/>\n"; flush();

	//---------------------------------------------------------------------------------------------
	//	check that all files in repository exist on local server
	//---------------------------------------------------------------------------------------------

	foreach($rList as $rUID => $item) { 
		$absFile = str_replace('//', '/', $kapenta->installPath . $item['relfile']);
		if (file_exists($absFile) == false) {
			echo "[*] $absFile is missing.<br/>\n";
			if (false == $repository->isExempt($item['relFile'])) { downloadFileFromRepository($item); }
		}
	}


	//---------------------------------------------------------------------------------------------
	//	get local list
	//---------------------------------------------------------------------------------------------

	$skipList = array();

	//---------------------------------------------------------------------------------------------
	//	list local files, compare to repository
	//---------------------------------------------------------------------------------------------

	$raw = shell_exec("find " . $kapenta->installPath);
	$lines = explode("\n", $raw);

	foreach($lines as $line) {
		//-----------------------------------------------------------------------------------------
		//	decide which ones to skip
		//-----------------------------------------------------------------------------------------

		$skip = false;
		if (trim($line) == '') { $skip = true; }						// must not be blank	
		$line = str_replace($kapenta->installPath, '/', $line);					// relative position
		$fileName = basename($line);									// get filename
		if (strpos(' ' . $fileName, '.') == false) { $skip = true; }	// filename must contain .

		// search for exemptions
		foreach ($repository->exemptions as $find) {
			if (strpos(' ' . $line, $find) != false) { $skip = true; }
			//echo "[i] Skipping: $line (security exemption) <br/>\n";
		}

		//-----------------------------------------------------------------------------------------
		//	compare hash with local file
		//-----------------------------------------------------------------------------------------

		if ((false == $skip) && (filesize($kapenta->installPath . $line) < 10000000)) {
			$itemUID = ''; 
			//TODO: use $kapenta for this
			$sha1 = sha1(implode(file($kapenta->installPath . $line)));

			//--------------------------------------------------------------------------------------
			//	compare to repository
			//--------------------------------------------------------------------------------------
			foreach($rList as $rUID => $item) 
				{ if ($item['relfile'] == $line) { $itemUID = $rUID; } }

			if ($itemUID == false) {
				//----------------------------------------------------------------------------------
				//	is not in repository, note this to user
				//----------------------------------------------------------------------------------
				if (substr($line, 0, 6) != '/data/') 
					{ echo "[>] not in repository: $line <br/>\n"; flush(); }
				

			} else {
				if ($sha1 != $rList[$itemUID]['hash']) {
					//------------------------------------------------------------------------------
					//	is different to version in repository, update it
					//------------------------------------------------------------------------------
					$relFile = $rList[$itemUID]['relfile'];
					echo "[i] this file should be updated: " . $relFile . " <br/>\n";
					downloadFileFromRepository($rList[$itemUID]);

				} else {
					//------------------------------------------------------------------------------
					//	files match
					//------------------------------------------------------------------------------
					//echo "[>] hashes match: " . $rList[$itemUID]['relfile'] . " <br/>\n";
					
				}

			}	// end if itemUID == false		

		} else { $skipList[] = $line; }

	} // end foreach line

	echo "<h1>Skipped Files</h1>\n";
	foreach ($skipList as $path) { echo $path . "<br/>\n"; }

//==================================================================================================
//	utility functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// download a single file from the repository
//--------------------------------------------------------------------------------------------------

function downloadFileFromRepository($item) {
	global $repository;
	global $kapenta;
	global $utils;

	$outFile = $item['relfile'];
	$outFile = str_replace('//', '/', $outFile);

	//----------------------------------------------------------------------------------------------
	//	create all folders
	//----------------------------------------------------------------------------------------------
	if ('folder' == $item['type']) {
		echo "[i] Creating directory $outFile <br/>\n";
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------

	if (true == $kapenta->fileExists($outFile)) { echo "[|] Replacing $outFile (already present)<br/>\n"; }
	else { echo "[|] Downloading $outFile (not present in local installation)<br/>\n"; }

	//----------------------------------------------------------------------------------------------
	//	download from repository door
	//----------------------------------------------------------------------------------------------
	$content = $utils->curlGet($repository->doorUrl . $item['uid'], '');

	if ($content == false) 
		{ echo "[*] Error: could not download $outFile (UID:" . $item['uid'] . ")<br/>\n";	} 
	else {
		//------------------------------------------------------------------------------------------
		//	content is base64 encoded
		//------------------------------------------------------------------------------------------
		$content = base64_decode($content);

		//------------------------------------------------------------------------------------------
		//	save it :-)
		//------------------------------------------------------------------------------------------
		$check = $kapenta->filePutContents($outFile, $content, false, false, 'w+');
		if ($check == false) {
			echo "[*] Error: could not open $outFile for writing.<br/>\n";	flush();
			return false;
		} else {
			echo "[>] Saving $outFile (UID:" . $item['uid'] . ") "
				 . "(type:" . $item['type'] . ")<br/>\n";	flush();
		} // end if cant write

	} // end if bad download

	return true;
}

?>
