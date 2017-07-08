<?

//--------------------------------------------------------------------------------------------------
//*	object for transferring large files between peers
//--------------------------------------------------------------------------------------------------
//+	When transferring large files between peers there can be issues with timeouts or memory
//+	exhaustion - this object helps by breaking a lerge file up into many smaller ones and
//+	transferring them piece by piece (see P2P worker object, started by cron).
//+	
//+	Metadata is stored in an XML file like the following:
//+
//+		<klargefile>	
//+			<path>data/videos/1/2/3/somewhere-over-the-rainbow.mp4</path>
//+			<hash>[sha1 hash of entire file]</hash>
//+			<size>34826104</size>
//+			<complete>no</complete>
//+			<parts>
//+				<part>
//+					<index>0</index>
//+					<hash>[sha1 hash of first 512k]</hash>
//+					<status>ok</status>
//+					<size>524288</size>
//+					<fileName>data/</fileName>
//+				</part>
//+				<part>
//+					<index>1</index>
//+					<hash>[sha1 hash of second 512k]</hash>
//+					<status>pending</status>
//+					<size>524288</size>
//+					<fileName>524288</fileName>
//+				</part>
//+				... more parts here ....
//+			</parts>
//+		</klargefile>
//+
//+	The metadata filename will be ./data/transfer/meta/filename-as-alias.xml.php

class KLargeFile {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $parts;				//%	array of file part metadata [array:dict]
	var $count = 0;			//%	number of parts in file [int]
	var $loaded = false;	//%	set to true if metadata loaded [bool]

	var $path = '';			//%	location of the file to be transferred / recieved [string]
	var $metaFile = '';		//%	relative to installPath [string]
	var $hash = '';			//%	sha1 hash of complete file [int]
	var $size = 0;			//%	total size of the file, bytes [int]
	var $partSize = 512;	//%	in kilobytes [int]
	var $complete = 'no';	//%	set to true when all parts have been transferred [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KLargeFile($path = '') {
		$this->parts = array();
		$this->path = $path;

		if ('' != $this->path) {
			$this->metaFile = $this->makeMetaFileName($path);
			$this->loadMetaXml();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load metadata XML file
	//----------------------------------------------------------------------------------------------
	//;	note that metaFile should be set before calling this
	//opt: xml - raw XML used instead of metaFile if given [string]
	//returns: true on success, false on failure [bool]

	function loadMetaXml($xml = '') {
		global $kapenta;
		if (false == $kapenta->fileExists($this->metaFile)) { return false; }
		$this->parts = array();

		$isFile = false; 
		if ('' == $xml) {									//	xml not given, load from disk 
			$xml = $this->metaFile;
			$isFile = true;
		}

		$xd = new KXmlDocument($xml, $isFile);

		$children = $xd->getChildren(1);					//%	children of root node [array]
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			switch(strtolower($child['type'])) {
				case 'path':		$this->path = $child['value'];			break;
				case 'hash':		$this->hash = $child['value'];			break;
				case 'size':		$this->size = $child['value'];			break;
				case 'complete':	$this->complete = $child['value'];		break;

				case 'parts':
					$parts = $xd->getChildren($childId);
					foreach($parts as $partId) { $this->parts[] = $xd->getChildren2d($partId); }
					break;	//......................................................................
			}
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save metadata XML file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function saveMetaXml() {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		$xml = $this->toXml();
		$check = $kapenta->fs->put($this->metaFile, $xml);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	make from an extant file
	//----------------------------------------------------------------------------------------------
	//;	note that $this->path should be set before this is called
	//returns: true on success, false on failure [bool]

	function makeFromFile() {
		global $kapenta;
		if (false == $kapenta->fs->exists($this->path)) { return false; }

		$absFile = $kapenta->installPath . $this->path;
		$this->hash = sha1_file($absFile);
		$this->size = filesize($absFile);
		$this->complete = 'yes';

		$this->parts = array();
		$numParts = ceil($this->size / ($this->partSize * 1024));

		for ($i = 0; $i < $numParts; $i++) {
			$raw = $this->getPart($i);
			$hash = sha1($raw);
			$fileName = 'data/transfer/parts/' . $kapenta->time() . '_' . $hash . '.part.php';
			$this->parts[$i] = array(
				'index' => $i,
				'status' => 'pending',
				'hash' => $hash,
				'size' => strlen($raw),
				'fileName' => $fileName
			);
		}
		//TODO: make the parts array

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if was have all parts
	//----------------------------------------------------------------------------------------------
	//returns: true if complete, false if not [bool]

	function checkCompletion() {
		$complete = 'yes';
		foreach($this->parts as $part) {
			if ('ok' != $part['status']) { $complete = 'no'; }
		}
		$this->complete = $complete;
		if ('yes' == $complete) { return true; }
		return false; 
	}

	//----------------------------------------------------------------------------------------------
	//.	rejoin parts into the original file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function stitchTogether() {
		global $kapenta;
		if ('no' == $this->complete) { return false; }

		//------------------------------------------------------------------------------------------
		//	base64 decode and join all parts
		//------------------------------------------------------------------------------------------
		$kapenta->filePutContents($this->path, '');					//	creates file and directories
		$fH = fopen($kapenta->installPath . $this->path, 'wb+');	//	open for writing
		if (false == $fH) { return false; }							//	if cannot create

		foreach($this->parts as $part) {
			$part64 = $kapenta->fs->get($part['fileName']);
			$partBin = base64_decode($part64);
			fwrite($fH, $partBin);
		}

		fclose($fH);

		//------------------------------------------------------------------------------------------
		//	make sure that it worked, delete if corrupt
		//------------------------------------------------------------------------------------------
		$newHash = sha1_file($kapenta->installPath . $this->path);
		if ($this->hash != $newHash) {
			unlink($kapenta->installPath . $this->path);
			return false;
		}
	
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a raw file segment
	//----------------------------------------------------------------------------------------------
	//returns: file part on success, empty string on failure [bool]

	function getPart($index) {
		global $kapenta;
		$raw = '';

		$fH = fopen($kapenta->installPath . $this->path, 'r');	//%	read only file handle [int]
		if (false == $fH) { return $raw; }

		$skip = ($this->partSize * 1024) * $index;				//%	position to skip to [int]
		$check = fseek($fH, $skip);								//	move to position
		if (-1 == $check) { fclose($fH); return $raw; }

		$raw = fread($fH, ($this->partSize * 1024));			//	read the part into $raw
		fclose($fH);

		return $raw;
	}

	//----------------------------------------------------------------------------------------------
	//.	save a file part to disk
	//----------------------------------------------------------------------------------------------
	//arg: index - part number [int]
	//arg: content64 - base64 encoded fiel part [string]
	//returns: true on success, false on failure [bool]

	function storePart($index, $content64, $hash) {
		global $kapenta;
		if (false == array_key_exists($index, $this->parts)) { echo "no such part<br/>"; return false; }
		if ($hash != $this->parts[$index]['hash']) { echo "hash mismatch<br/>"; return false; }
		$fileName = $this->parts[$index]['fileName'];

		$check = $kapenta->fs->put($fileName, $content64, true, true);
		if (false == $check) { echo "file could not be saved<br/>"; return false; }

		$this->parts[$index]['status'] = 'ok';
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert file location to alias
	//----------------------------------------------------------------------------------------------
	//arg: path - location relative to installPath [string]
	//returns: filename based on path name [string]

	function makeMetaFileName($path) {
		$path = str_replace('/', '-fs-', $path);
		$path = str_replace('\\', '-bs-', $path);
		$path = 'data/transfer/meta/' . $path . '.xml.php';
		return $path;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize metadata to xml
	//----------------------------------------------------------------------------------------------

	function toXml() {
		$parts = "\t<parts>\n";
		foreach($this->parts as $part) {
			$parts .= ''
			 . "\t\t<part>\n"
			 . "\t\t\t<index>" . $part['index'] . "</index>\n"
			 . "\t\t\t<hash>" . $part['hash'] . "</hash>\n"
			 . "\t\t\t<status>" . $part['status'] . "</status>\n"
			 . "\t\t\t<size>" . $part['size'] . "</size>\n"
			 . "\t\t\t<fileName>" . $part['fileName'] . "</fileName>\n"
			 . "\t\t</part>\n";
		}
		$parts .= "\t</parts>\n";

		$xml = ''
		 . "<klargefile>\n"
		 . "\t<path>" . $this->path . "</path>\n"
		 . "\t<size>" . $this->size . "</size>\n"
		 . "\t<complete>" . $this->size . "</complete>\n"
		 . $parts
		 . "</klargefile>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	render metadata in HTML for viewing / debugging
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		global $theme;
		$html = '';												//%	return value [string]
		$table = array();										//%	html table [array:array:string]

		$table[] = array('Index', 'Hash', 'Status', 'Size', 'File Name');
		foreach($this->parts as $idx => $p) {
			$table[] = array($p['index'], $p['hash'], $p['status'], $p['size'], $p['fileName']);
		}

		$loaded = 'yes';
		if (false == $this->loaded) { $loaded = 'no'; }

		$html .= ''
		. "<b>path:</b> " . $this->path . "<br/>\n"
		. "<b>count:</b> " . $this->count . "<br/>\n"
		. "<b>loaded:</b> " . $loaded . "<br/>\n"
		. "<b>metaFile:</b> " . $this->metaFile . "<br/>\n"
		. "<b>size:</b> " . $this->size . "<br/>\n"
		. "<b>partSize:</b> " . $this->partSize . " (kb)<br/>\n"
		. "<b>complete:</b> " . $this->complete . "<br/>\n"
		. "<br/><b>Parts:</b><br/>\n"
		. $theme->arrayToHtmlTable($table, true, true);

		return $html;
	}

}

?>
