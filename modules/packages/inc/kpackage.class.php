<?

//--------------------------------------------------------------------------------------------------
//*	object for interacting with repository a kapenta repository
//--------------------------------------------------------------------------------------------------
//+ TODO: this could use some tightening up.
//+	TODO: add preinstall and postinstall hooks - each should accept an $args array and return HTML
//+
//+	Kapenta package manifest XML file example:
//+
//+	<kpackage>
//+		<uid>123456789</uid>
//+		<name>Example-Package</name>
//+		<description>Plaintext</description>
//+		<version>2</version>
//+		<revision>123</revision>
//+		<updated>2011-07-08 11:22:42</updated>
//+		<source>http://my-repository.com/code/</source>
//+		<files>
//+			<file>
//+				<uid>9876543421</uid>
//+				<hash>sha1000abc123abc123abc123000sha1</hash>
//+				<type>image/gif</type>
//+				<size>6789</size>
//+				<path>morbo.gif</path>
//+			</file>
//+			<file>
//+				<uid>1111111</uid>
//+				<hash>sha1000abc321abc321abc321000sha1</hash>
//+				<type>kapenta/action</type>
//+				<size>789</size>
//+				<path>modules/example/actions/default.act.php</path>
//+			</file>
//+		</files>
//+		<dependancies>
//+			<dependency>
//+				<uid>454545454545</uid>
//+				<version>1</version>
//+				<revision>23</revision>
//+			</dependency>
//+		</dependencies>
//+		<filter>
//+			<include>{start}module/example/</include>
//+			<include>{start}morbo.gif</include>
//+			<exclude>~{end}</exclude>
//+		</filter>
//+	</kpackage>
//+
//+	Dependancies specify the minimum version and revision with which a module will work.
//+
//+	TODO: add package microformat for embedding in webpages
//+	
//+	<div class='kpackage'>
//+		<span class='kpackage-uid'>123456789</span>
//+		<span class='kpackage-name'>Example-Package</span>
//+		<span class='kpackage-url'>http://repository.com/code/</span>
//+		<span class='kpackage-version'>2</span>
//+		<span class='kpackage-revision'>134</span>
//+	</div>

class KPackage {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $UID = '';			//_	unique ID of a package
	var $source = '';		//_	repository url [string]
	var $version = '';		//_	package version [string]
	var $revision = '';		//_	package revision [string]
	var $username = '';		//_	user [string]
	var $password = '';		//_	password for repository [string]
	var $updated = '';		//_	datetime when package last changed [string]
	var $type = 'kapenta';	//_	repository type (kapenta|git|svn) [string]
	
	var $status = '';		//_	state of the package on this kapenta instance [string]
	var $dirty = '';		//_	if partially installed [string]
	var $date = '';			//_	last update to package [string]

	var $manifestUrl = '';	//_	location of package manifest [string]
	var $checkoutUrl = '';	//_	location from which new updates should be downloaded [string]
	var $commitUrl = '';	//_	location to which new updates should be posted [string]

	var $fileName = '';		//_	local file where manifest is cached [string]
	var $loaded = false;	//_	set to true when loaded [bool]
	var $recent = false;	//_	set to true if manifest less than 24 hours old [bool]

	var $name;				//_	name of package [string]
	var $description;		//_	description of package [string]
	var $installFile = '';	//_	install script for this file [string]
	var $installFn = '';	//_	function to call to run installation [string]

	var $includes;			//_	patterns to match when comitting [array]
	var $excludes;			//_	patterns to match when comitting [array]
	var $dependencies;		//_	packages on which this one depends [array]
	var $files;				//_	files defined in this package [array:array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: UID - alias or UID of a package in the repository [string]
	//arg: lazy - if true then do not load XML immediately [bool]

	function KPackage($UID = '', $lazy = false) {
		global $kapenta;
		global $kapenta;

		$this->includes = array();
		$this->excludes = array();
		$this->files = array();
		$this->dependencies = array();

		$this->UID = $UID;
		$this->name = $kapenta->registry->get('pkg.' . $UID . '.name');
		$this->version = $kapenta->registry->get('pkg.' . $UID . '.v');
		$this->revision = $kapenta->registry->get('pkg.' . $UID . '.r');

		$this->source = $kapenta->registry->get('pkg.' . $UID . '.source');
		$this->status = $kapenta->registry->get('pkg.' . $UID . '.status');
		$this->username = $kapenta->registry->get('pkg.' . $UID . '.user'); 
		$this->password = $kapenta->registry->get('pkg.' . $UID . '.pass');
		$this->dirty = $kapenta->registry->get('pkg.' . $UID . '.dirty');
		$this->date = $kapenta->registry->get('pkg.' . $UID . '.date');

		if (('' != $UID) && ('' != $this->source)) {
			// TODO: check trailing slash
			$this->source = $this->source;
			$this->manifestUrl = $this->source . 'manifest/' . $UID; 
			$this->commitUrl = $this->source . 'commit/'; 
		}

		if (('' != $UID) && (false == $lazy)) {
			$this->fileName = 'data/packages/' . $UID . '.xml.php';
			if (true == $kapenta->fs->exists($this->fileName)) { 
				$this->loaded = $this->loadXml($this->fileName, true); 
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	load XML manifest
	//----------------------------------------------------------------------------------------------
	//arg: xml - raw XML or file name relative to install path [string]
	//opt: isFile - set to true if filename is passed and not XML, default is false [bool]
	//returns: true on success, false on failue [bool]

	function loadXml($xml, $isFile = false) {
		$doc = new KXMLDocument($xml, $isFile);
		if (false == $doc->loaded) { return false; }		
		$this->files = array();

		$kids = $doc->getChildren(1);		//%	1 is the root node [array]
		foreach($kids as $childId) {
			$child = $doc->getEntity($childId);
			//TODO: sanitize these values
			switch(strtolower($child['type'])) {
				case 'uid':			$this->UID = $child['value'];				break;
				case 'name':		$this->name = $child['value'];				break;
				case 'version':		$this->version = $child['value'];			break;
				case 'revision':	$this->revision = $child['value'];			break;
				case 'installfile':	$this->installFile = $child['value'];		break;
				case 'installfn':	$this->installFn = $child['value'];			break;
				case 'updated':		$this->updated = $child['value'];			break;
				case 'source':		$this->source = $child['value'];			break;
				case 'description':	$this->description = $child['value'];		break;

				case 'files':
					$files = $doc->getChildren($childId);
					foreach($files as $fileId) {
						$meta = $doc->getChildren2d($fileId);
						//TODO: more checks here
						if (true == array_key_exists('uid', $meta)) {
							if ((true == array_key_exists('hash', $meta))&&('' != $meta['hash'])) {
								$this->files[$meta['uid']] = $meta;
							}
						}						
					}
					break;		//..................................................................

				case 'dependancies':
					$deps = $doc->getChildren($childId);
					foreach($deps as $depId) {
						$dependency = $doc->getChildren2d($depId);
						//TODO: more checks here
						if (true == array_key_exists('uid', $dependency)) {
							$this->dependencies[$dependency['uid']] = $dependency;
						}
					}
					break;		//..................................................................

				case 'filter':
					$this->includes = array();					// 	clear existing filter
					$this->excludes = array('.svn/', '~{end}');	//	always filter svn caches
					$filter = $doc->getChildren($childId);
					foreach($filter as $filterId) {
						$entity = $doc->getEntity($filterId);
						switch(strtolower($entity['type'])) {

							case 'include':	
								if (false == in_array($entity['value'], $this->includes)) {
									$this->includes[] = $entity['value']; 
								}
								break;

							case 'exclude':
								if (false == in_array($entity['value'], $this->excludes)) {
									$this->excludes[] = $entity['value'];
								}
								break;

						}
					}
					break;		//..................................................................

			}
		}		

		if ('' == $this->UID) { return false; }
		if ('' == $this->name) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save XML manifest
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location to save at, relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function saveXml($fileName) {
		global $kapenta;
		$xml = $this->toXml();
		$check = $kapenta->fs->put($fileName, $xml, true, true);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object as XML
	//----------------------------------------------------------------------------------------------

	function toXml() {
		$xml = '';				//%	return value [string]

		$filter = '';
		foreach($this->includes as $include) { $filter .= "\t\t<include>$include</include>\n"; }
		foreach($this->excludes as $exclude) { $filter .= "\t\t<exclude>$exclude</exclude>\n"; }

		$files = '';
		foreach($this->files as $file) {
			$files .= "\t\t"
			 . "<file>"
			 . "<uid>" . $file['uid'] . "</uid>"
			 . "<hash>" . $file['hash'] . "</hash>"
			 . "<type>" . $file['type'] . "</type>"
			 . "<size>" . $file['size'] . "</size>"
			 . "<path>" . $file['path'] . "</path>"
			 . "</file>\n";
		}

		$dependencies = '';
		foreach($this->dependencies as $dependency) {
			$dependencies .= "\t\t"
			 . "<dependency>"
			 . "<uid>" . $dependency['uid'] . "</uid>"
			 . "<version>" . $dependency['version'] . "</version>"
			 . "<revision>" . $dependency['revision'] . "</revision>"
			 . "</dependency>\n";
		}

		$xml .= ''
		 . "<package>\n"
		 . "\t<uid>" . $this->UID . "</uid>\n"
		 . "\t<name>" . $this->name . "</name>\n"
		 . "\t<source>" . $this->source . "</source>\n"
		 . "\t<description>" . $this->description . "</description>\n"
		 . "\t<version>" . $this->version . "</version>\n"
		 . "\t<revision>" . $this->revision . "</revision>\n"
		 . "\t<installfile>" . $this->installFile . "</installfile>\n"
		 . "\t<installfn>" . $this->installFn . "</installfn>\n"
		 . "\t<updated>" . $this->updated . "</updated>\n"
		 . "\t<files>\n"
		 . $files
		 . "\t</files>\n"
		 . "\t<filter>\n"
		 . $filter
		 . "\t</filter>\n"
		 . "\t<dependencies>\n"
		 . $dependencies
		 . "\t</dependencies>\n"
		 . "</package>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	make array for use by blocks
	//----------------------------------------------------------------------------------------------
	//arg: 

	function extArray() {
		global $kapenta;

		$ext = array(
			'UID' => $this->UID,
			'name' => $this->name,
			'description' => $this->description,
			'source' => $this->source,
			'version' => $this->version,
			'revision' => $this->revision,
			'status' => $this->status,
			'installFile' => $this->installFile,
			'installFn' => $this->installFn,
			'updated' => $this->updated,
			'username' => $this->username,
			'password' => $this->password,
			'dirty' => $this->dirty
		);

		if ('' == $ext['description']) { $ext['description'] = 'none'; }
		$ext['descriptionHtml'] = str_replace("\n", "<br/>\n", $ext['description']);

		$ext['includes'] = implode("\n", $this->includes);
		$ext['excludes'] = implode("\n", $this->excludes);

		$ext['repUrl'] = $ext['source'] . 'package/' . $this->UID;
		$ext['repLink'] = "<a href='" . $ext['repUrl'] . "'>[repository]</a>";

		$ext['manifestUrl'] = '';
		$ext['manifestLink'] = '';
		$ext['installForm'] = '';	

		if ('installed' == $kapenta->registry->get('pkg.' . $ext['UID'] . '.status')) {
			$ext['manifestUrl'] = '%%serverPath%%packages/showpackage/' . $this->UID;
			$ext['manifestLink'] = "<a href='" . $ext['manifestUrl'] . "'>[local manifest]</a>";
		} else {
			$ext['installForm'] = '[[:packages::installpackageform::UID=' . $ext['UID'] . ':]]';
		}

		$ext['bgcolor'] = '';
		if ('yes' == $ext['dirty']) { $ext['bgcolor'] = "class='title'"; }

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	download latest manifest from repository and save
	//----------------------------------------------------------------------------------------------
	//;	this will set dirty flag via call to getLocalDifferent()
	//returns: true on success, false on failure [bool]

	function updateFromRepository() {
		global $utils;	
		global $kapenta;

		$xml = $utils->curlGet($this->manifestUrl);
		if (false == strpos($xml, '</package>')) { return false; }
		$this->loadXml($xml);
		$this->saveXml($this->fileName);

		$prefix = 'pkg.' . $this->UID . '.';

		$kapenta->registry->set($prefix . 'source', $this->source);
		$kapenta->registry->set($prefix . 'uid', $this->UID);
		$kapenta->registry->set($prefix . 'name', $this->name);
		$kapenta->registry->set($prefix . 'v', $this->version);
		$kapenta->registry->set($prefix . 'r', $this->revision);
		$kapenta->registry->set($prefix . 'date', $this->updated);

		$this->getLocalDifferent();			//	check for files which do not match manifest
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	download all outstanding or outdated files from repository
	//----------------------------------------------------------------------------------------------
	//returns: html report [string]
	//TODO: move this from update.act.php?

	function updateAllFiles() {
		/*
		$report = '';					//%	return value [string]
		$downloads = array();			//%	items to be downloaded from repository [array:string]

		if (false == $this->loaded) { $report .= 'Package not initialized.<!-- error --><br/>'; }
		if (0 == count($this->files)) { $report .= "No files to download.<br/>"; }

		//------------------------------------------------------------------------------------------
		//	make list of files to be downloaded / updated
		//------------------------------------------------------------------------------------------
		$localList = $this->getLocalList();
		foreach($this->files as $file) {
			if (false == array_key_exists($file['uid'], $localList)) {
				$downloads[] = $file['uid'];			// does not exist on local instance
			} else {
				if ($file['hash'] != $localList[$file['uid']]['hash']) {
					$downloads[] = $file['uid'];
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	do it
		//------------------------------------------------------------------------------------------
		*/
	}

	//----------------------------------------------------------------------------------------------
	//.	download a single file from the repository and update local copy
	//----------------------------------------------------------------------------------------------
	//arg: uid - UID of file on repository [string]
	//returns: true on success, false on failure [bool]

	function updateFile($uid) {
		global $kapenta;
		global $session;
		global $utils;

		if (false == array_key_exists($uid, $this->files)) { return false; }
		$file = $this->files[$uid];

		$url = $this->source . 'checkout/' . $uid;
		$raw64 = $utils->curlGet($url);
		//TODO: look for error XML

		$raw = base64_decode($raw64);
		$rawHash = sha1($raw);
		//$session->msg("raw64:<br/><textarea rows='10' cols='60' style='width:100%'>$raw64</textarea>");
		//$session->msg("raw:<br/><textarea rows='10' cols='60' style='width:100%'>$raw</textarea>");
		$session->msg("hash: " . $file['hash'] . "<br/>check: " . $rawHash);

		if ($file['hash'] != $rawHash) { return false; }

		$session->msg("hashes match.", 'ok');

		$check = $kapenta->fs->put($file['path'], $raw);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	examine local files and create list - same format as this->files
	//----------------------------------------------------------------------------------------------
	//;	'local' member may be 'same' or 'different'

	function getLocalList() {
		global $kapenta;
		global $updateManager;
		$files = array();

		//------------------------------------------------------------------------------------------
		//	list all files and folders from local installPath, filter with includes and excludes
		//------------------------------------------------------------------------------------------
		$list = array();								//%	all files and folders [array:string]
		$matching = array();							//%	belong to this package [array:string]
		
		if (true == isset($updateManager)) {
			$list = $updateManager->getAllFiles();
		} else {
			$list = $kapenta->fs->search('', '', true);
		}

		foreach($list as $item) {
			$add = false;
			$match = ' {start}' . trim($item) . '{end}';

			foreach($this->includes as $find) {
				if (('' != $find) && (false != strpos($match, $find))) { $add = true; }
			}

			foreach($this->excludes as $find) {	
				if (('' != $find) && (false != strpos($match, $find))) { $add = false; } 
			}

			if (true == $add) { $matching[] = $item; }
		}

		//------------------------------------------------------------------------------------------
		//	get metdata for matching files
		//------------------------------------------------------------------------------------------
		foreach($matching as $item) {
			// look up UID of this file or create one if new
			$uid = $kapenta->createUID();
			$local = 'different';
			$hash = $this->getFileHash($item);

			foreach($this->files as $meta) {
				if ($meta['path'] == $item) { 
					$uid = $meta['uid'];
					if ($meta['hash'] == $hash) { $local = 'same'; }
				}
			}

			$files[] = array(
				'uid' => $uid,
				'hash' => $hash,
				'type' => $this->guessFileType($item),
				'size' => filesize($kapenta->installPath . $item),
				'path' => $item,
				'local' => $local
			);

		}

		return $files;
	}

	//----------------------------------------------------------------------------------------------
	//.	get list of package files which are different on local instance, or not in manifest
	//----------------------------------------------------------------------------------------------
	//returns: set of files which differ from what package manifest would predict [array]

	function getLocalDifferent() {
		global $kapenta;
		global $kapenta;

		$different = array();						//%	return value [array:array:string]
		$localFiles = $this->getLocalList();		//%	according to filter [array:array:string]

		foreach($localFiles as $uid => $file) {		//	finds hash mismatches and extraneous files
			if ('different' == $file['local']) { $different[$uid] = $file; }
		}

		foreach($this->files as $uid => $file) {	//	finds missing files
			if (false == $kapenta->fs->exists($file['path'])) { 
				$file['local'] = 'missing';
				$different[$uid] = $file;
			}
		}

		if (0 == count($different)) { $kapenta->registry->set('pkg.' . $this->UID . '.dirty', 'no'); }
		else { $kapenta->registry->set('pkg.' . $this->UID . '.dirty', 'yes'); }

		return $different;
	}

	//----------------------------------------------------------------------------------------------
	//.	compare local and repository lists to create a list of files to be updated
	//----------------------------------------------------------------------------------------------
	//arg: repositoryList - list of files from the repository [array]
	//arg: localList - list of local files [array]
	//returns: list of files to be uploaded to the repository [array]	

	function makeUploadList($repositoryList, $localList) {
		//TODO: rewrite this
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
				if ($repositoryList[$litem['uid']]['hash'] != $litem['hash']) 
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
		//TODO: reimplement
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
		//TODO: reimplement
		global $kapenta;
		global $db;

		echo "[i] storing file - path: $path type: $type <br/>\n";flush();

		//------------------------------------------------------------------------------------------
		//	load the file
		//------------------------------------------------------------------------------------------
		$raw = implode(file($kapenta->installPath . $path));
		$dirname = str_replace("\\", '', dirname($path)) . '/';
		$description = '(automatically uploaded ' . $db->datetime() . ')';
		$isbinary = 'no';
		$binary = '';
		$content = '';

		//------------------------------------------------------------------------------------------
		//	binary files are attached
		//------------------------------------------------------------------------------------------

		$binTypes = array('jpeg', 'png', 'gif', 'ttf', 'swf');		//TODO: registry setting

		if (true == in_array($type, $binTypes)) {
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
		global $kapenta;
		$hash = '';
		$absFile = str_replace('//', '/', $kapenta->installPath . $relFile);

		if (true == is_dir($absFile)) { $hash = sha1($relFile); } 
		else { $hash = sha1_file($absFile); }

		return $hash;
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
			'.txt' => 'text/plain',
			'.js' => 'text/javascript',
			'.css' => 'text/css',
			'.html' => 'text/html',
			'.htm' => 'text/html',
			'.jpeg' => 'image/jpeg',
			'.jpg' => 'image/jpeg',
			'.png' => 'image/png',
			'.gif' => 'image/gif',
			'.ttf' => 'font/ttf',
			'module.xml.php' => 'kapenta/modulexml', 
			'.template.php' => 'kapenta/template', 
			'.mod.php' => 'kapenta/model',
			'.set.php' => 'kapenta/set',
			'.act.php' => 'kapenta/action',  
			'.page.php' => 'kapenta/page',
			'.fn.php' => 'kapenta/view', 
			'.block.php' => 'kapenta/block',
			'.class.php' => 'kapenta/class',
			'.inc.php' => 'kapenta/include',
			'.xml.php' => 'kapenta/xml',
			'.on.php' => 'kapenta/event',
			'.cmd.php' => 'kapenta/shellcmd',
			'.dbd.php' => 'kapenta/database',
			'.sh.php' => 'kapenta/shell',
			'.swf' => 'application/x-shockwave-flash',
			'.php' => 'text/php',
			'.xml' => 'text/xml'
		);

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
		global $theme;

		$newCount = 0;									//%	number of files in list [int]
		$table = array();								//%	2d string array [array:array:string]

		$table[] = array('UID', 'Hash', 'Type', 'Size', 'Path');
		foreach($list as $it) {
			if (($it['uid'] == '') && ($it['hash'] != '')) { $newCount++; }
			$table[] = array($it['UID'], $it['hash'], $it['type'], $it['size'], $it['path']);
		}
		$html = $theme->arrayToHtmlTable();

		if ($newCount > 0) { 
			$html .= "$newCount files are new to the repository and will be assigned UIDs.<br/>\n"; 
		}

		return $html;
	}

	//==============================================================================================
	//	MICROFORMAT - a TODO for making it easier to share kapenta modules on the web
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	look for kapenta package microformat
	//----------------------------------------------------------------------------------------------
	//arg: html - source of web page to search [string]
	//returns: array of package definitions found [array:array:string]

	function parseHtml($html) {
		$packages = array();						//%	return value [array:array:string]
		$html = str_replace("\"", "'", $html);	

		$match = "<div class='kpackage'>";			//%	denotes start of microfotmat [string]
		$startPos = 1;
		$endPos = 1;

		while ($startPos != false) {
			$startPos = strpos($html, $match);
			if (false == $startPos) { break; }
			$endPos = strpos($html, "</div>", $startPos);
			if (false == $endPos) { break; }
			//TODO: extract 
		}

		return $packages;
	}

	//==============================================================================================
	//	COMMIT - methods for updating a package on the repository
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	set the username and password for committing to repository, or downloading private packages
	//----------------------------------------------------------------------------------------------
	//arg: username - repository account username [string]
	//arg: password - repository account password [string]
	//returns: true on success, false on failure [bool]

	function setCredentials($username, $password) {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		if ('' != trim($username)) { $kapenta->registry->set('pkg.' . $this->UID . '.user'); }
		if ('' != trim($username)) { $kapenta->registry->set('pkg.' . $this->UID . '.pass'); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	test credentials
	//----------------------------------------------------------------------------------------------
	//arg: privilege - privilege required on the repository (commit) [string]
	//returns: true if privilege exists, false if not [bool]

	function testCredentials($privilege) {
		global $kapenta;
		$postvars = array(
			'mode' => 'basic',
			'username' => $this->username,
			'password' => $this->password,
			'packageUID' => $this->UID,
			'privilege' => $privilege,
			'return' => 'xml'
		);

		$result = $kapenta->utils->curlPost($this->source . 'testcredentials/', $postvars);
		if ('<ok/>' == $result) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	update package metadata on repository
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function postToRepository() {
		global $utils;
		global $session;
		global $kapenta;

		$ext = $this->extArray();

		//	update username and password from registry

		$this->username = $kapenta->registry->get('pkg.' . $this->UID . '.user');
		$this->password = $kapenta->registry->get('pkg.' . $this->UID . '.pass');

		$postvars = array(
			'mode' => 'basic',
			'username' => $this->username,
			'password' => $this->password,
			'packageUID' => $this->UID,
			'privilege' => 'commit',
			'return' => 'xml',
			'installFile' => $this->installFile,
			'installFn' => $this->installFn,
			'includes' => $ext['includes'],
			'excludes' => $ext['excludes']
		);

		$result = $kapenta->utils->curlPost($this->source . 'updatepackage/', $postvars);
		
		if ('<ok/>' == $result) { 
			//echo "Package setting  updated successfully.<br/>"; flush();
			return true; 
		} else { 
			$msg = "<textarea rows='10' cols='40' style='width: 100%;'>$result</textarea>";
			$kapenta->session->msg($msg, 'warn');
			echo $msg . "<br/>\n";
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add changelog message
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function postChangelogMessage($message, $files) {
		global $utils;
		$postvars = array(
			'mode' => 'basic',
			'username' => $this->username,
			'password' => $this->password,
			'packageUID' => $this->UID,
			'privilege' => 'commit',
			'return' => 'xml',
			'message' => $message,
			'files' => $files
		);

		$result = $utils->curlPost($this->source . 'addchange/', $postvars);
		//echo 'Posting to: ' . $this->source . 'addchange/<br/>';
		//echo 'Repository returns: ' . $utils->cleanTitle($result). "<br/>";
		if ('<ok/>' == $result) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	commit a file to repository
	//----------------------------------------------------------------------------------------------
	//arg: meta - file metadata array [dict]
	//arg: description - brief summary of file [string]
	//arg: message - commit message [string]
	//returns: true on success, false on failure [bool]

	function commit($meta, $description, $message) {
		global $utils;
		global $kapenta;		

		if (false == $kapenta->fs->exists($meta['path'])) { return false; }		

		$raw = $kapenta->fs->get($meta['path']);
		$content = base64_encode($raw);
		$content = wordwrap($content, 80);

		$postvars = array(
			'mode' => 'basic',
			'username' => $this->username,
			'password' => $this->password,
			'packageUID' => $this->UID,
			'privilege' => 'commit',
			'return' => 'xml',
			'fileUID' => $meta['uid'],
			'path' => $meta['path'],
			'title' => basename($meta['path']),
			'type' => $meta['type'],
			'content' => $content,
			'description' => $description,
			'isBinary' => 'no',
			'message' => $message,
			'size' => $meta['size'],
			'hash' => $meta['hash']
		);

		$result = $utils->curlPost($this->source . 'commit/', $postvars);
		echo "<div class='chatmessageblack'>Posting to: " . $this->source . 'commit/<br/>';
		echo 'Repository returns: ' . str_replace('<', '&lt;', $result) . "<br/></div>\n";
		if ('<ok/>' == $result) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get file description for commit
	//----------------------------------------------------------------------------------------------

	function getFileDescription($path, $type) {
		global $theme;
		$description = '';		//%	return value [string]
		$mode = 'none';

		switch($type) {
			case 'kapenta/action':		$mode = 'comments';		break;
			case 'kapenta/view':		$mode = 'comments';		break;
			case 'kapenta/model':		$mode = 'comments';		break;
			case 'kapenta/set':			$mode = 'comments';		break;
			case 'kapenta/class':		$mode = 'comments';		break;
			case 'kapenta/include':		$mode = 'comments';		break;
			case 'kapenta/event':		$mode = 'comments';		break;
			case 'kapenta/include':		$mode = 'comments';		break;
			case 'kapenta/shellcmd':	$mode = 'comments';		break;
			case 'kapenta/database':	$mode = 'comments';		break;
			case 'text/javascript':		$mode = 'comments';		break;
			case 'kapenta/template':	$description = 'Template (theme).';			break;
			case 'kapenta/page':		$description = 'XML Page Template.';		break;
			case 'kapenta/block':		$description = 'Block/View Template.';		break;
			case 'kapenta/modulexml':	$description = 'XML Module definition.';	break;
			case 'text/xml':			$description = 'XML File.';					break;
		}

		if ('comments' == $mode) {
			$block = '[[:admin::codesummary::path=' . $path . ':]]';
			$description = $theme->expandBlocks($block, '');
		}

		return $description;
	}

}

?>
