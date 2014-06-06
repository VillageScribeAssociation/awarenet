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
//+			<module>videos</module>
//+			<model>videos_video</model>
//+			<UID>123456789</UID>
//+			<path>data/videos/1/2/3/somewhere-over-the-rainbow.mp4</path>
//+			<hash>[sha1 hash of entire file]</hash>
//+			<size>34826104</size>
//+			<complete>no</complete>
//+			<mismatches>0</mismatches>
//+			<parts>
//+				<part>
//+					<index>0</index>
//+					<hash>[sha1 hash of first 512k]</hash>
//+					<status>ok</status>
//+					<size>524288</size>
//+					<fileName>data/p2p/transfer/parts/[uid]_[hash]_[idx].part</fileName>
//+				</part>
//+				<part>
//+					<index>1</index>
//+					<hash>[sha1 hash of second 512k]</hash>
//+					<status>pending</status>
//+					<size>524288</size>
//+					<fileName>data/p2p/transfer/parts/[uid]_[hash]_[idx].part</fileName>
//+				</part>
//+				... more parts here ....
//+			</parts>
//+		</klargefile>
//+
//+	The metadata filename will be ./data/p2p/transfer/meta/{sha1hash}.xml.php

class KLargeFile {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $parts;				//%	array of file part metadata [array:dict]
	var $count = 0;			//%	number of parts in file [int]
	var $loaded = false;	//%	set to true if metadata loaded [bool]

	var $module = '';		//%	module responsible for this file [string]
	var $model = '';		//%	type of object which owns this file [string]
	var $UID = '';			//%	UID of object which owns this file [string]
	var $hasOwner = false;	//%	owner object is known to this instance [bool]

	var $path = '';			//%	location of the file to be transferred / received [string]
	var $metaFile = '';		//%	relative to installPath [string]
	var $hash = '';			//%	sha1 hash of complete file [int]
	var $size = 0;			//%	total size of the file, bytes [int]
	var $chunkSize = 512;	//%	in kilobytes [int]
	var $complete = 'no';	//%	set to true when all parts have been transferred [string]
	var $mismatches = 0;	//%	records failures, so bad manifests can be corrected [int]
	var $timestamp = 0;		//%	records when this manifest was created, for allow expiry [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: path - ideal location of file this relates to [string]

	function KLargeFile($path = '', $hash = '', $refUID = '') {
		global $kapenta;

		$this->parts = array();

		$this->path = $path;			//	TODO: checks on validity of all these
		$this->hash = $hash;
		$this->UID = $refUID;
		$this->timestamp = $kapenta->time();

		if (('' != $this->hash) && ('' != $this->UID)) {
			$this->metaFile = $this->makeMetaFileName($hash, $this->UID);
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
		$isFile = false; 
		$this->parts = array();
		$this->timestamp = 0;

		if ('' == $xml) {
			// xml not given, try to load from disk
			if (false == $kapenta->fs->exists($this->metaFile)) { return false; }
			$xml = $this->metaFile;
			$isFile = true;
		}

		$xd = new KXmlDocument($xml, $isFile);

		$children = $xd->getChildren(1);					//%	children of root node [array]

		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			switch(strtolower($child['type'])) {
				case 'module':		$this->module = $child['value'];			break;
				case 'model':		$this->model = $child['value'];				break;
				case 'uid':			$this->UID = $child['value'];				break;

				case 'path':		$this->path = $child['value'];				break;
				case 'hash':		$this->hash = $child['value'];				break;
				case 'size':		$this->size = $child['value'];				break;
				case 'complete':	$this->complete = $child['value'];			break;
				case 'mismatches':	$this->mismatches = (int)$child['value'];	break;
				case 'timestamp':	$this->timestamp = (int)$child['value'];	break;

				case 'parts':
					$parts = $xd->getChildren($childId);
					foreach($parts as $partId) { $this->parts[] = $xd->getChildren2d($partId); }
					break;	//......................................................................
			}
		}

		$this->metaFile = $this->makeMetaFileName($this->hash, $this->UID);
		$this->loaded = true;
		if (0 == count($this->parts)) { $this->loaded = false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save metadata XML file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function saveMetaXml() {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		if ('' == $this->UID) {
			echo "Could not save file: " . $this->metaFile . " (missing UID)\n";			
			return false;
		}
		$xml = $this->toXml();
		$check = $kapenta->fs->put($this->metaFile, $xml);
		if (false == $check) {
			echo "Could not save file: " . $this->metaFile . " (" . strlen($xml) . " bytes)\n";
		}
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	make from an extant file
	//----------------------------------------------------------------------------------------------
	//;	note that $this->path should be set before this is called
	//;
	//arg: module - name of module responsible for this file [string]
	//arg: model - type of object which owns this file [string]
	//arg: UID - UID of object which owns this file [string]
	//returns: true on success, false on failure [bool]

	function makeFromFile() {
		global $kapenta;
		global $kapenta;

		$owner = $kapenta->fs->getOwner($this->path);

		if (0 == count($owner)) {
			$this->loaded = false;
			return false;
		}							//	not shareable

		$this->module = $owner['module'];
		$this->model = $owner['model'];
		$this->UID = $owner['UID'];

		if (!$kapenta->fs->exists($this->path)) { return false; }			//	no such file
		if (!$kapenta->moduleExists($this->module)) { return false; }		//	no such module
		if (!$kapenta->db->objectExists($this->model, $this->UID)) { return false; }	//	no such object
		if (!$kapenta->db->isShared($this->model, $this->UID)) { return false; }		//	is not shared

		$this->hash = $kapenta->fileSha1($this->path);
		$this->size = $kapenta->fs->size($this->path);
		$this->complete = 'yes';

		$this->parts = array();
		$numParts = ceil($this->size / ($this->chunkSize * 1024));

		for ($i = 0; $i < $numParts; $i++) {
			$raw = $this->getPart($i);
			$hash = sha1($raw);
			$fileName = ''
			 . 'data/p2p/transfer/parts/'
			 . $this->UID . '_' . $this->hash . '_' . $i . '.part.php';

			$this->parts[$i] = array(
				'index' => $i,
				'status' => 'pending',
				'hash' => $hash,
				'size' => strlen($raw),
				'fileName' => $fileName
			);
		}

		$this->metaFile = $this->makeMetaFileName($this->hash, $this->UID);
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
	//.	get percentage complete
	//----------------------------------------------------------------------------------------------

	function percentComplete() {
		$total = count($this->parts);
		$complete = 0;
		foreach($this->parts as $part) { if ('ok' == $part['status']) { $complete++; } }
		$percent = floor(($complete / $total) * 100);
		return $percent;
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
		$kapenta->fs->put($this->path, '');					//	creates file and directories
		$fH = fopen($kapenta->installPath . $this->path, 'wb+');	//	open for writing
		if (false == $fH) { return false; }							//	if cannot create

		foreach($this->parts as $part) {
			$part64 = $kapenta->fs->get($part['fileName'], true, true);
			$partBin = base64_decode($part64);
			fwrite($fH, $partBin);
		}

		fclose($fH);

		//------------------------------------------------------------------------------------------
		//	make sure that it worked, delete if corrupt
		//------------------------------------------------------------------------------------------
		$newHash = sha1_file($kapenta->installPath . $this->path);
		if ($this->hash != $newHash) {
			echo "HASH MISMATCH $newHash != " . $this->hash . "<br/>";
			unlink($kapenta->installPath . $this->path);
			return false;
		}
	
		return true;
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

		$skip = ($this->chunkSize * 1024) * $index;				//%	position to skip to [int]
		$check = fseek($fH, $skip);								//	move to position
		if (-1 == $check) { fclose($fH); return $raw; }

		$raw = fread($fH, ($this->chunkSize * 1024));			//	read the part into $raw
		fclose($fH);

		return $raw;
	}

	//----------------------------------------------------------------------------------------------
	//.	save a file part to disk
	//----------------------------------------------------------------------------------------------
	//arg: index - sequence number of part [int]
	//arg: content64 - base64 encoded file part [string]
	//arg: hash - sha1 hash of raw file part [string]
	//returns: true on success, false on failure [bool]

	function storePart($index, $content64, $hash) {
		global $kapenta;
		if (false == array_key_exists($index, $this->parts)) {
			//echo "no such part<br/>";
			return false;
		}

		if ($hash != $this->parts[$index]['hash']) {
			//echo "hash mismatch<br/>";
			return false;
		}

		$fileName = $this->parts[$index]['fileName'];

		$check = $kapenta->fs->put($fileName, $content64, true, true);
		if (false == $check) { echo "file could not be saved<br/>"; return false; }

		$this->parts[$index]['status'] = 'ok';
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	record a mismatch between hash of download part and expected item
	//----------------------------------------------------------------------------------------------

	function recordMismatch() {
		$this->mismatches += 1;
		$this->saveMetaXml();
	}

	//----------------------------------------------------------------------------------------------
	//.	convert file location to alias
	//----------------------------------------------------------------------------------------------
	//arg: hash - sha1 hash of file [string]
	//arg: UID - UID of owner object, disambiguate multiple identical files [string]
	//returns: filename based on path name [string]

	function makeMetaFileName($hash, $UID) {
		$hash = str_replace(array('.', '/', '\\'), array('', '', ''), $hash);
		$UID = str_replace(array('.', '/', '\\'), array('', '', ''), $UID);
		$path = 'data/p2p/transfer/meta/' . $UID . '_' . $hash . '.xml.php';
		return $path;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove manifest and any downloaded parts
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $kapenta;
		$allOk = true;

		foreach($this->parts as $part) {
			if (true == $kapenta->fs->exists($part['fileName'])) {
				$check = $kapenta->fileDelete($part['fileName']);
				if (false == $check) {
					$allOk = false;
				} else {
					//echo "removed part: " . $part['fileName'] . "<br/>\n";
				}
			}
		}

		$check = $kapenta->fileDelete($this->metaFile);
		if (false == $check) {
			//echo "could not delete meta file: " . $this->metaFile . "<br/>\n";
			$allOk = false;
		} else {
			//echo "removed meta file: " . $this->metaFile . "<br/>\n";
		}
		return $allOk;
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
		 . "\t<module>" . $this->module . "</module>\n"
		 . "\t<model>" . $this->model . "</model>\n"
		 . "\t<UID>" . $this->UID . "</UID>\n"
		 . "\t<path>" . $this->path . "</path>\n"
		 . "\t<size>" . $this->size . "</size>\n"
		 . "\t<complete>" . $this->size . "</complete>\n"
		 . "\t<hash>" . $this->hash . "</hash>\n"
		 . "\t<mismatches>" . $this->mismatches . "</mismatches>\n"
		 . "\t<timestamp>" . $this->timestamp . "</timestamp>\n"
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
		. "<b>partSize:</b> " . $this->chunkSize . " (kb)<br/>\n"
		. "<b>complete:</b> " . $this->complete . "<br/>\n"
		. "<br/><b>Parts:</b><br/>\n"
		. $theme->arrayToHtmlTable($table, true, true);

		return $html;
	}

}

?>
