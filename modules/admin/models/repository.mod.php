<?

//--------------------------------------------------------------------------------------------------
//*	object for interacting with repository
//--------------------------------------------------------------------------------------------------
//+ TODO: this could use some tightening up.

class CodeRepository {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $listUrl = '';		// location of list of objects [string]
	var $postUrl = '';		// location to post objects to [string]
	var $doorUrl = '';		// location to post objects to [string]
	var $key = '';			// authorises post  [string]

	var $exemptions;	// array of locations which are not uploaded/updated [array]
	var $skipped;		// list of local files which will not be uploaded [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: url - location of a code repository module [string]
	//arg: raUID - recordAlias or UID of a project in the repository [string]

	function CodeRepository($url, $raUID) {
		$this->listUrl = $url . "projectlist/" . $raUID . '/';
		$this->postUrl = $url . 'commit/';
		$this->doorUrl = $url . 'projectfile/';
		$this->key = $postKey;
		$this->exemptions = array();
	}

	//----------------------------------------------------------------------------------------------
	//.	add an exemption
	//----------------------------------------------------------------------------------------------
	//arg: match - filenames matching this will be dropped [string] 

	function addExemption($match) {
		if (in_array($match, $this->exemptions) == false) {	$this->exemptions[] = $match; }
	}

	//----------------------------------------------------------------------------------------------
	//.	get exemptions array
	//----------------------------------------------------------------------------------------------

	function getExemptions() { return $this->exemptions; }

	//----------------------------------------------------------------------------------------------
	//.	clear the exemptions array
	//----------------------------------------------------------------------------------------------

	function clearExemptions() { $this->exemptions = array(); }

	//----------------------------------------------------------------------------------------------
	//.	download repository list and convert into an array
	//----------------------------------------------------------------------------------------------
	//returns: array of file metadata - uid, hash, type and relfile [array]

	function getRepositoryList() {
		$rlist = array();
		$raw = curlGet($this->listUrl);	
		$lines = explode("\n", $raw);
		foreach($lines as $line) {
			$cols = explode("\t", $line);
			$item = array(
				'uid' => $cols[0],
				'hash' => $cols[1],
				'type' => $cols[2],
				'relfile' => $cols[3]
			);

			$rlist[$item['uid']] = $item;
		}
		return $rlist;
	}

	//----------------------------------------------------------------------------------------------
	//.	examine local files and create list - same format as $this->getRepositoryList()
	//----------------------------------------------------------------------------------------------
	//arg: repositoryList - list of files as returned by the repository [array]
	//,	any exemptions should be set up before this is called

	function getLocalList($repositoryList) {
		global $installPath;
		$localList = array();
		
		//------------------------------------------------------------------------------------------
		//	list all files and folders from local installPath
		//------------------------------------------------------------------------------------------
		$raw = shell_exec("find $installPath");
		$lines = explode("\n", $raw);

		foreach($lines as $line) {
			//--------------------------------------------------------------------------------------
			//	decide which ones to skip
			//--------------------------------------------------------------------------------------
			$skip = false;
			if (trim($line) == '') { $skip = true; }						// must not be blank	
			$line = str_replace($installPath, '/', $line);					// relative position
			$fileName = basename($line);									// get filename

			foreach($this->exemptions as $find) {							// search for exemptions
				if (strpos(' ' . $line, $find) != false) { $skip = true; }
			}

			//--------------------------------------------------------------------------------------
			//	is a valid file, add to local list
			//--------------------------------------------------------------------------------------
			if ($skip == false) {
				$newItem = array();				
				$newItem['uid'] = '';										// not set at this stage
				$newItem['hash'] = $this->getFileHash($line);
				$newItem['type'] = $this->guessFileType($line);
				$newItem['relfile'] = $line;

				//----------------------------------------------------------------------------------
				//	add trailing slash to folders
				//----------------------------------------------------------------------------------
				if ($newItem['type'] == 'folder') 
					{ $newItem['relfile'] = str_replace('//', '/', $newItem['relfile'] . '/'); }

				//----------------------------------------------------------------------------------
				//	compare to repository list, find UID if present
				//----------------------------------------------------------------------------------
				foreach($repositoryList as $item) { 
					if ($item['relfile'] == $newItem['relfile']) { $newItem['uid'] = $item['uid']; }
				}

				//----------------------------------------------------------------------------------
				//	add new item to list of local files
				//----------------------------------------------------------------------------------				
				$localList[] = $newItem;
			}

//			if ($skip == false) {
//				$itemUID = ''; 
//				$sha1 = sha1(implode(file($installPath . $line)));
//
//				//----------------------------------------------------------------------------------
//				//	compare to repository
//				//----------------------------------------------------------------------------------
//				foreach($rList as $rUID => $item) 
//					{ if ($item['relfile'] == $line) { $itemUID = $rUID; } }
//
//				if ($itemUID == false) {
//					//------------------------------------------------------------------------------
//					//	is not in repository, add it
//					//------------------------------------------------------------------------------
//					echo "[>] adding $line to repository (new file)<br/>\n"; flush();
//					storeNewFile($postfile, $line, $respoitorykey);
//
//				} else {
//					if ($sha1 != $rList[$itemUID]['sha1']) {
//						//--------------------------------------------------------------------------
//						//	is different to version in repository, update it
//						//--------------------------------------------------------------------------
//
//					} else {
//						//--------------------------------------------------------------------------
//						//	files match
//						//--------------------------------------------------------------------------
//					}
//
//				}	// end if itemUID == false		
//	
//			} else { $skipList[] = $line; }

		} // end foreach line

//		echo "<h1>Skipped Files</h1>\n";
//		foreach ($skipList as $path) { echo $path . "<br/>\n"; }

		return $localList;

	}

	//----------------------------------------------------------------------------------------------
	//.	compare local and repository lists to create a list of files to be updated
	//----------------------------------------------------------------------------------------------
	//arg: repositoryList - list of files from the repository [array]
	//arg: localList - list of local files [array]
	//returns: list of files to be uploaded to the repository [array]	

	function makeUploadList($repositoryList, $localList) {
		$uploadList = array();
		foreach($localList as $litem) {
			if ($litem['uid'] == '') { 
				//----------------------------------------------------------------------------------
				// new items
				//----------------------------------------------------------------------------------
				$uploadList[] = $litem; 

			} else {
				//----------------------------------------------------------------------------------
				// updated items (hash does hot match)
				//----------------------------------------------------------------------------------
				if ($reopsitoryList[$litem['uid']]['hash'] != $litem['hash']) 
					{ $uploadList[] = $litem; }

			}

		}
		return $uploadList;
	}

	//----------------------------------------------------------------------------------------------
	//.	perform uploads
	//----------------------------------------------------------------------------------------------
	//arg: uploadList - list of files to upload, see makeUploadList [array]

	function doUploads($uploadList) {
		foreach ($uploadList as $item) {	
			storeFile($item['relFile'], $item['type'], $item['hash']);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	save a new file to the repository
	//----------------------------------------------------------------------------------------------
	//arg: path - relative to $installPath [string]
	//arg: type - file type [string]
	//arg: hash - sha1 file hash [string]

	function storeFile($path, $type, $hash) {
		global $installPath;
	
		echo "[i] storing file - path: $path type: $type <br/>\n";flush();

		//------------------------------------------------------------------------------------------
		//	load the file
		//------------------------------------------------------------------------------------------
		$raw = implode(file($installPath . $path));
		$dirname = str_replace("\\", '', dirname($path)) . '/';
		$description = '(automatically uploaded ' . mysql_datetime() . ')';
		$isbinary = 'no';
		$binary = '';
		$content = '';

		//------------------------------------------------------------------------------------------
		//	binary files are attached
		//------------------------------------------------------------------------------------------
		if (($type == 'jpeg')||($type == 'png')||($type == 'gif')||($type == 'ttf')) {
			$isbinary = 'yes';
			$binary = base64_encode($raw);
			$content = base64_encode('(binary file attached)');
		} else {
			$content = base64_encode($raw);
		}

		//------------------------------------------------------------------------------------------
		//	assemble postvars
		//------------------------------------------------------------------------------------------
		$postVars = array(
						'action' => 'storeNode',
						'key' => $respoitorykey,
						'path' => $dirname,
						'type' => $type,
						'title' => basename($path),
						'description' => base64_encode($description),
						'content' => $content,
						'isbinary' => $isbinary,
						'binary' => $binary,
						'hash' => $hash
					);

		//------------------------------------------------------------------------------------------
		//	do it
		//------------------------------------------------------------------------------------------
		$ch = curl_init($postfile);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		echo "[i] server responds: <small> $response </small> <br/>\n";flush();

	}

	//----------------------------------------------------------------------------------------------
	//.	get the sha1 hash of a file or folder
	//----------------------------------------------------------------------------------------------
	//, give location relative to installPath
	//arg: relFile - file location relative to $installPath [string]
	//returns: sha1 hash of a file or path [string]

	function getFileHash($relFile) {
		global $installPath;
		$absFile = str_replace('//', '/', $installPath . $relFile);
		if (is_dir($absFile) == true) { return sha1($relFile); } 
		else { return sha1(implode(file($absFile))); }
	}

	//----------------------------------------------------------------------------------------------
	//.	decide which type a file is
	//----------------------------------------------------------------------------------------------
	//arg: path - path including filename [string]
	//returns: file type or false if it can't guess [string][bool]

	function guessFileType($path) {
		if (strpos($path, '.') == false) { return 'folder'; }
		$path = strrev($path) . 'xxxxxxxxxxxxxx';
		$types = array(
			'.txt' => 'txt',
			'.js' => 'txt',
			'.css' => 'txt',
			'.html' => 'txt',
			'.htm' => 'txt',
			'.jpeg' => 'jpeg',
			'.jpg' => 'jpeg',
			'.png' => 'png',
			'.gif' => 'gif',
			'.ttf' => 'ttf',
			'.template.php' => 'template', 
			'.page.php' => 'page', 
			'.block.php' => 'block',
			'.xml.php' => 'xml',
			'.php' => 'php' );

		foreach($types as $ext => $type) {
			if (substr($path, 0, strlen($ext)) == strrev($ext)) { return $type; }
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	return a list of objects as an html table
	//----------------------------------------------------------------------------------------------
	//arg: list - a set of file locations and metadata [array]
	//returns: html [string]

	function listToHtml($list) {
		$newCount = 0;
		$html = "<table>\n"
				. "\t<tr>\n"
				. "\t\t<td><b>UID</b></td>\n"
				. "\t\t<td><b>HASH</b></td>\n" 
				. "\t\t<td><b>TYPE</b></td>\n" 
				. "\t\t<td><b>RELFILE</b></td>\n" 
				. "\t</tr>\n";

		foreach($list as $item) {
			if (($item['uid'] == '') && ($item['hash'] != '')) { $newCount++; }
			$html .= "\t<tr>\n";
			$html .= "\t\t<td><small>" . $item['uid'] . "</small></td>\n";
			$html .= "\t\t<td><small>" . $item['hash'] . "</small></td>\n";
			$html .= "\t\t<td><small>" . $item['type'] . "</small></td>\n";
			$html .= "\t\t<td><small>" . $item['relfile'] . "</small></td>\n";
			$html .= "\t</tr>\n";
		}

		$html .= "</table><br/>\n";

		if ($newCount > 0) { 
			$html .= "$newCount files are new to the repository and will be assigned UIDs.<br/>\n"; 
		}

		return $html;
	}

}

?>
