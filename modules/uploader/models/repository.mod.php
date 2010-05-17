<?

//--------------------------------------------------------------------------------------------------
//*	object for interacting with kapenta code repository
//--------------------------------------------------------------------------------------------------

class CodeRepository {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	public $list = '';		// location of list of objects [string]
	public $post = '';		// location to post objects to [string]
	public $key = '';		// authorises post [string]

	private $exemptions;	// array of locations which are not uploaded [array]
	public $skipped;		// list of local files which will not be uploaded [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: listUrl - URL of repository file list [string]
	//arg: postUrl - URL of repository posting interface [string]
	//arg: postKey - repository password [string]

	function CodeRepository($listUrl, $postUrl, $postKey) {
		$this->list = $listUrl;
		$this->post = $postUrl;
		$this->key = $postKey;
		$this->exemptions = array();
	}

	//----------------------------------------------------------------------------------------------
	//.	add an exemption
	//----------------------------------------------------------------------------------------------
	//arg: match - filenames/paths containing this string will be ignored [string]

	function addExemption($match) {
		if (in_array($match, $this->exemptions) == false) {	$this->exemptions[] = $match; }
	}

	//----------------------------------------------------------------------------------------------
	//.	get list of exemptions
	//----------------------------------------------------------------------------------------------
	//returns: current list of exemptions [array]

	function getExemptions() { return $this->exemptions; }

	//----------------------------------------------------------------------------------------------
	//.	clear list of exemptions
	//----------------------------------------------------------------------------------------------

	function clearExemptions() { $this->exemptions = array(); }

	//----------------------------------------------------------------------------------------------
	//.	download repository list and convert into an array
	//----------------------------------------------------------------------------------------------
	//returns: list of files maintained in the respository and their metadata [array]

	function getRepositoryList() {
		$rlist = array();
		$raw = implode(file($this->list));	
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
	//.	examine local files and create list - same format as getRepositoryList()
	//----------------------------------------------------------------------------------------------
	//arg: repositoryList - as produced by getRepositoryList(), for matching UIDs of items [array]
	//returns: list of files in the local installation and their metadata [array]	
	//, any exemptions should be set up before this is called

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
	//arg: repositoryList - a list of files [array]	
	//arg: localList - a list of files [array]
	//returns: list of files to be created/updated in the repository [array]

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

	function doUploads($uploadList) {
		foreach ($uploadList as $item) {	
			storeFile($item['relFile'], $item['type'], $item['hash']);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	save a new file (or directory) to the repository
	//----------------------------------------------------------------------------------------------
	//arg: path - relative to installPath [string]
	//arg: type - file type, as per guessFileType() [string]
	//arg: hash - sha1 hash of file (or directory) [string]

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
	//.	get the sha1 hash of a file
	//----------------------------------------------------------------------------------------------
	//arg: relFile - file location relative to installPath [string]
	//returns: sha1 hash of file [string]

	function getFileHash($relFile) {
		global $installPath;
		//TODO: error checking, traversal checking, use centralised IO functions
		$absFile = str_replace('//', '/', $installPath . $relFile);
		$raw = implode(file($absFile));		
		$hash = sha1($relFile . $raw);		
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	decide which type a file is
	//----------------------------------------------------------------------------------------------
	//arg: path - filename including path [string]
	//returns: best guess at file type, or false if not determined [string][bool]

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
	//arg: list - list of files [array]
	//returns: html [string]
	//, TODO: use arrayToHtmlTable

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
