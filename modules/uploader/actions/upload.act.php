<?

//--------------------------------------------------------------------------------------------------
//	uploading code to repository on kapenta.org.uk
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }
	require_once($installPath . 'modules/admin/models/repository.mod.php');

	//----------------------------------------------------------------------------------------------
	//	set up repository access
	//----------------------------------------------------------------------------------------------

	//$repositoryUrl = 'http://kapenta.co.za/code/';
	$repositoryUrl = 'http://www.kapenta.org.uk/code/';
	$projectUID = '106665425118526027';
	$respoitoryKey = '66awarenet99';

	$repository = new CodeRepository($repositoryUrl, $projectUID);

	$repository->addExemption("setup.inc.php");				// dynamically generated on install
	$repository->addExemption("uploader/");					// this module (CONTAINS KEY)
	$repository->addExemption("install/");					// defunct
	$repository->addExemption(".svn");						// sybversion files and directories
	$repository->addExemption("data/log/e");					// ?
	$repository->addExemption("svnadd.sh");					// ?
	$repository->addExemption("svndelete.sh");				// ?
	$repository->addExemption("data/log/e");				// ?
	$repository->addExemption(".log.php");					// log files
	$repository->addExemption("phpinfo.php");					// log files
	$repository->addExemption("~");							// gedit revision files
	$repository->addExemption("/drawcache/");				// dynamically generated images

	for ($i = 0; $i < 10; $i++) {
		$repository->addExemption("data/images/" . $i);		// user images
		$repository->addExemption("data/files/" . $i);		// user files
		$repository->addExemption("data/log/" . $i);		// user files
		$repository->addExemption("data/temp/" . $i);		// user files
	}

	//----------------------------------------------------------------------------------------------
	//	get list of files from respoitory
	//----------------------------------------------------------------------------------------------

	$rList = $repository->getRepositoryList();

	//----------------------------------------------------------------------------------------------
	//	get local list
	//----------------------------------------------------------------------------------------------

	$skipList = array();

	//----------------------------------------------------------------------------------------------
	//	list local files, compare to repository
	//----------------------------------------------------------------------------------------------

	$raw = shell_exec("find $installPath");
	$lines = explode("\n", $raw);

	foreach($lines as $line) {
		//------------------------------------------------------------------------------------------
		//	decide which ones to skip
		//------------------------------------------------------------------------------------------

		$skip = false;
		if (trim($line) == '') { $skip = true; }						// must not be blank	
		$line = str_replace($installPath, '/', $line);					// relative position
		$fileName = basename($line);									// get filename
		if (strpos(' ' . $fileName, '.') == false) { $skip = true; }	// filename must contain .

		$exempt = $repository->getExemptions();
		foreach($exempt as $find) {										// search for exemptions
			if (strpos(' ' . $line, $find) != false) { $skip = true; }
		}

		if ($skip == false) {
			$itemUID = ''; 
			$sha1 = sha1(implode(file($installPath . $line)));

			//--------------------------------------------------------------------------------------
			//	compare to repository
			//--------------------------------------------------------------------------------------
			foreach($rList as $rUID => $item) 
				{ if ($item['relfile'] == $line) { $itemUID = $rUID; } }

			if ($itemUID == false) {
				//----------------------------------------------------------------------------------
				//	is not in repository, add it
				//----------------------------------------------------------------------------------
				echo "[>] adding $line to repository (new file)<br/>\n"; flush();
				storeNewFile($line, $respoitoryKey);

			} else {
				if ($sha1 != $rList[$itemUID]['hash']) {
					//------------------------------------------------------------------------------
					//	is different to version in repository, update it
					//------------------------------------------------------------------------------
					echo "[i] this file should be updated: " . $rList[$itemUID]['relfile'] . " ($sha1 != " . $rList[$itemUID]['hash'] . ") <br/>\n";
					storeNewFile($line, $respoitoryKey);

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
//	store a file to the repository
//--------------------------------------------------------------------------------------------------

function storeNewFile($path, $respoitorykey) {
	global $installPath;
	global $repository;

	//----------------------------------------------------------------------------------------------
	//	guess file type
	//----------------------------------------------------------------------------------------------
	$type = $repository->guessFileType($path);
	if ($type == false) { 
		echo "[*] indeterminate file type: $path<br/>\n";flush();
		return false; 
	}
	echo "[i] storing file - path: $path type: $type <br/>\n";flush();

	//----------------------------------------------------------------------------------------------
	//	load the file
	//----------------------------------------------------------------------------------------------
	$raw = implode(file($installPath . $path));
	$dirname = str_replace("\\", '', dirname($path)) . '/';
	$description = '(automatically uploaded ' . mysql_datetime() . ')';
	$isbinary = 'no';
	$binary = '';
	$content = '';

	//----------------------------------------------------------------------------------------------
	//	binary files are attached
	//----------------------------------------------------------------------------------------------
	if (($type == 'jpeg')||($type == 'png')||($type == 'gif')||($type == 'ttf')) {
		$isbinary = 'yes';
		$binary = base64_encode($raw);
		$content = base64_encode('(binary file attached)');
	} else {
		$content = base64_encode($raw);
	}

	//----------------------------------------------------------------------------------------------
	//	calculate hash
	//----------------------------------------------------------------------------------------------

	$hash = $repository->getFileHash($path);
	echo "[i] file hash: $hash ($path) <br/>\n";flush();

	//----------------------------------------------------------------------------------------------
	//	assemble postvars
	//----------------------------------------------------------------------------------------------
	$postVars = array(
					'action' => 'storeNode',
					'key' => $respoitorykey,
					'dirname' => $dirname,
					'path' => $path,
					'type' => $type,
					'title' => basename($path),
					'description' => base64_encode($description),
					'content' => $content,
					'isbinary' => $isbinary,
					'binary' => $binary,
					'hash' => $hash
				);

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	$ch = curl_init($repository->postUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);

	echo "[i] server responds: <small> $response </small> <br/>\n";flush();

}

?>
