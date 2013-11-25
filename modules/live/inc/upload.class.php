<?

//--------------------------------------------------------------------------------------------------
//*	object for storing large files as they are uploaded piecewise by JS uploader
//--------------------------------------------------------------------------------------------------
//+	When uploading large files 
//+
//+	Metadata is stored in an XML file like the following:
//+
//+		<upload>	
//+			<name>[base64_encoding or original name]</path>
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
//+		</upload>
//+
//+	The metadata filename will be ./data/live/uploads/hash.xml.php

class Live_Upload {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $fileName = '';		//_	where metadata is stored, relative to installPath [string]
	var $fileType = '';		//_	general category of this file (video|image|all) [string]
	var $extension = '';	//_	file extension, lowercase [string]
	var $outFile = '';		//_	where the file will be built when all parts are received [string]
	var $parts;				//_	array of file part metadata [array:dict]
	var $count = 0;			//_	number of parts in file [int]
	var $loaded = false;	//_	set to true if metadata loaded [bool]

	var $refModule = '';	//_	kapenta module file will be attached to [string]
	var $refModel = '';		//_	type of object file will be attached to [string]
	var $refUID = '';		//_	UID of object file will be attached to [string]

	var $hash = '';			//_	sha1 hash of complete file [int]
	var $name = '';			//_	name of original file, not used by this [string]
	var $size = 0;			//_	total size of the file, bytes [int]
	var $partSize = 512;	//_	in kilobytes [int]
	var $complete = 'no';	//_	set to true when all parts have been transferred [string]
	var $started = '';		//_	datetime when upload began [string]
	var $updated = '';		//_	datetime when last part was sent [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: hash - sha1 hash of individual part hashes [string]

	function Live_Upload($hash = '') {
		global $kapenta;

		$this->parts = array();
		$this->started = $kapenta->datetime(); 
		$this->updated = $kapenta->datetime();

		if ('' != $hash) {
			$this->fileName = 'data/live/uploads/' . $hash . '.xml.php';
			$this->outFile = 'data/live/uploads/' . $hash . '.xxx';
			$this->loadXml();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load metadata XML file
	//----------------------------------------------------------------------------------------------
	//;	note that $this->fileName should be set before calling this
	//opt: xml - raw XML used instead of metaFile if given [string]
	//returns: true on success, false on failure [bool]

	function loadXml($xml = '') {
		global $kapenta;
		if (('' == $xml) && (false == $kapenta->fs->exists($this->fileName))) { return false; }
		$this->parts = array();

		$isFile = false; 
		if ('' == $xml) {									//	xml not given, load from disk 
			$xml = $this->fileName;
			$isFile = true;
		}

		$xd = new KXmlDocument($xml, $isFile);

		$children = $xd->getChildren(1);					//%	children of root node [array]
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			switch(strtolower($child['type'])) {
				case 'refmodule':	$this->refModule = $child['value'];				break;
				case 'refmodel':	$this->refModel = $child['value'];				break;
				case 'refuid':		$this->refUID = $child['value'];				break;
				case 'name':		$this->name = base64_decode($child['value']);	break;
				case 'hash':		$this->hash = $child['value'];					break;
				case 'size':		$this->size = $child['value'];					break;
				case 'complete':	$this->complete = $child['value'];				break;
				case 'started':		$this->started = $child['value'];				break;
				case 'updated':		$this->updated = $child['value'];				break;
				case 'outfile':		$this->outfile = $child['value'];				break;
				case 'type':		$this->fileType = $child['value'];				break;
				case 'extension':	$this->extension = $child['value'];				break;

				case 'parts':
					$parts = $xd->getChildren($childId);
					foreach($parts as $partId) { 
						$part = $xd->getChildren2d($partId);
						if (false == array_key_exists('fileName', $part)) {
							$part['fileName'] = ''
							 . 'data/live/fileparts/' . $this->hash . '_'
							 . $part['index'] . '_' . $part['hash'] . '.txt.php';
						}
						$this->parts[] = $part; 
					}
					break;	//......................................................................
			}
		}

		$this->loaded = true;
		if (0 == count($this->parts)) { $this->loaded = false; }
		$this->checkExtant();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save metadata XML file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function saveXml() {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		$xml = $this->toXml();
		$check = $kapenta->fs->put($this->fileName, $xml);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if was have all parts
	//----------------------------------------------------------------------------------------------
	//returns: true if complete, false if not [bool]

	function checkCompletion() {
		$complete = 'yes';
		foreach($this->parts as $part) {
			if ('done' != $part['status']) { $complete = 'no'; }
		}
		$this->complete = $complete;
		if ('yes' == $complete) { return true; }
		return false; 
	}

	//----------------------------------------------------------------------------------------------
	//.	check that we actually have any parts which are marked as 'done'
	//----------------------------------------------------------------------------------------------

	function checkExtant() {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		foreach($this->parts as $idx => $part) {
			if ('done' == $part['status']) {
				if (false == $kapenta->fs->exists($part['fileName'])) {
					$this->parts[$idx]['status'] = 'waiting';
					$this->saveXml();
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	rejoin parts into the original file
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function stitchTogether() {
		global $kapenta;
		//if ('no' == $this->complete) { return false; }
		
		$kapenta->fs->put($this->outFile, '');					//	create file
		$fH = fopen($kapenta->installPath . $this->outFile, 'wb+');		//	open for writing
		//echo "opening outfile: " . $this->outFile . "<br/>";
		if (false == $fH) { return false; }								//	if cannot create

		foreach($this->parts as $part) {
			$part64 = $kapenta->fs->get($part['fileName'], true, true);
			//echo "part64: <br/>";
			//echo "<textarea rows='10' style='width: 100%'>$part64</textarea><br/>\n";

			$partBin = base64_decode($part64);
			//echo "partBin: <br/>";
			//echo "<textarea rows='10' style='width: 100%'>$partBin</textarea><br/>\n";
			fwrite($fH, $partBin);
		}

		fclose($fH);

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a raw file segment
	//----------------------------------------------------------------------------------------------
	//TODO: remove if not needed
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
		if (false == array_key_exists($index, $this->parts)) { return false; }
		if ($hash != $this->parts[$index]['hash']) { return false; }
		$fileName = $this->parts[$index]['fileName'];

		$check = $kapenta->fs->put($fileName, $content64, true, true);
		if (false == $check) { echo "file could not be saved<br/>"; return false; }

		$this->parts[$index]['status'] = 'done';
		return true;
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
		 . "\t<refModule>" . $this->refModule . "</refModule>\n"
		 . "\t<refModel>" . $this->refModel . "</refModel>\n"
		 . "\t<refUID>" . $this->refUID . "</refUID>\n"
		 . "\t<name>" . base64_encode($this->name) . "</name>\n"
		 . "\t<size>" . $this->size . "</size>\n"
		 . "\t<hash>" . $this->hash . "</hash>\n"
		 . "\t<complete>" . $this->size . "</complete>\n"
		 . "\t<started>" . $this->started . "</started>\n"
		 . "\t<updated>" . $this->updated . "</updated>\n"
		 . "\t<outfile>" . $this->outFile . "</outfile>\n"
		 . "\t<type>" . $this->fileType . "</type>\n"
		 . "\t<extension>" . $this->extension . "</extension>\n"
		 . $parts
		 . "</klargefile>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	make bitmap representing downloaded (1) / pending (0) file parts [string]
	//----------------------------------------------------------------------------------------------
	//returns: pseudo-bitmap of uploaded parts, base64_encoded, eg <b>001001101</b> [string]
	
	function getBitmap() {

	}

	//----------------------------------------------------------------------------------------------
	//.	temporary / development method
	//----------------------------------------------------------------------------------------------
	//TODO: replace with actual parts bitmap 
	//returns: pseudo-bitmap of uploaded parts, eg 001001101 [string]

	function getBitmapTemp() {
		$bmp = '';
		$count = count($this->parts);
		for ($i = 0; $i < $count; $i++) {
			if ('done' == $this->parts[$i]['status']) { $bmp .= '1'; } else { $bmp .= '0'; }
		}
		return $bmp;
	}


	//----------------------------------------------------------------------------------------------
	//.	delete manifest and any parts
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $kapenta;

		foreach($this->parts as $part) {
			if (true == $kapenta->fs->exists($part['fileName'])) { 
				$kapenta->fileDelete($part['fileName'], true);
			}
		}

		$check = $kapenta->fileDelete($this->fileName);
		return $check;
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
		. "<b>name:</b> " . $this->name . "<br/>\n"
		. "<b>count:</b> " . $this->count . "<br/>\n"
		. "<b>loaded:</b> " . $loaded . "<br/>\n"
		. "<b>fileName:</b> " . $this->fileName . "<br/>\n"
		. "<b>size:</b> " . $this->size . "<br/>\n"
		. "<b>partSize:</b> " . $this->partSize . " (kb)<br/>\n"
		. "<b>complete:</b> " . $this->complete . "<br/>\n"
		. "<b>started:</b> " . $this->started . "<br/>\n"
		. "<b>updated:</b> " . $this->updated . "<br/>\n"
		. "<br/><b>Parts:</b><br/>\n"
		. $theme->arrayToHtmlTable($table, true, true);

		return $html;
	}

}

?>
