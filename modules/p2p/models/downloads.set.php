<?

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	object to record the files we are currently downloading
//--------------------------------------------------------------------------------------------------

class P2P_Downloads {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;			//_	array of file locations [array:string]
	var $loaded = false;	//_	set to true when downloads loaded [bool]

	var $fileName = '';		//_	file storing the list of downloads [string]
	var $peerUID = '';		//_	ref P2P_Peer [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: peerUID - UID of the peer from which we are recieveing this file [string]

	function P2P_Downloads($peerUID = '') {
		$this->members = array();
		$this->peerUID = $peerUID;
		if ('' != $peerUID) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load list of files we want from this peer
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $kapenta;
		$this->members = array();
		if ('' == $this->peerUID) { return false; }

		$this->fileName = 'data/p2p/transfer/' . $this->peerUID . '.txt.php';
		if (false == $kapenta->fs->exists($this->fileName)) { return false; }
		$raw = $kapenta->fs->get($this->fileName, true, true);
		$lines = explode("\n", $raw);

		foreach($lines as $line) {
			if (('' != trim($line)) && (false == in_array($line, $this->members))) {
				$this->members[] = $line;
			}
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save list of downloads to disk
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function save() {
		global $kapenta;

		if ('' == $this->peerUID) { return false; }
		$raw = '';
		$this->fileName = 'data/p2p/transfer/' . $this->peerUID . '.txt.php';

		foreach($this->members as $download) {
			if (strlen($download) > 20) { $raw .= $download . "\n"; }
		}
		$check = $kapenta->fs->put($this->fileName, $raw, true, true);

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a download to the collection
	//----------------------------------------------------------------------------------------------
	//arg: path - location of this file we want from a peer [string]
	//returns: true on success, false on failure [bool]

	function add($path) {
		$check = false;
		if (true == in_array($path, $this->members)) { return $check; }
		$this->members[] = $path;
		$check = $this->save();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a download from the collection
	//----------------------------------------------------------------------------------------------
	//arg: path - location of this file we want from a peer [string]
	//returns: true on success, false on failure [bool]

	function remove($path) {
		if (false == in_array($path, $this->members)) { return false; }
		$found = false;
		$members = array();

		foreach($this->members as $download) {
			if ($download == $path) { $found = true; }
			else { $members[] = $download; }
		}

		if (false == $found) { return false; }

		$this->members = $members;
		$check = $this->save();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	print this list as HTML
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		$html = "<h2>Downloads from " . $this->peerUID . "</h2>\n";
		foreach($this->members as $download) { $html .= $download . "<br/>\n"; }
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a download has a manifest
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location of file relative to installPath [string]
	//returns: true if manifest exists, false if not [bool]

	function hasManifest($fileName) {
		$klf = new KLargeFile($fileName);
		return $klf->loaded;
	}

	//----------------------------------------------------------------------------------------------
	//.	pull manifest from this peer
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location of file on remote peer relative to its installPath [string]
	//returns: true on sucess, false on failure [bool]

	function pullManifest($fileName) {
		if ('' == $this->peerUID) { return false; }
		$peer = new P2P_Peer($this->peerUID);
		if (false == $peer->loaded) { return false; }
		echo "Downlading manifest from peer " . $peer->url . "p2p/file/ ...<br/>\n";
		$xml = $peer->sendMessage('file', $fileName);
		echo "<textarea rows='10' style='width:100%;'>$xml</textarea>\n";
		$klf = new KLargeFile($fileName);
		$klf->loadMetaXml($xml);

		if (false == $klf->loaded) {
			if (false !== strpos($xml, "<error>File not found.</error>")) {
				// peer doesn't have the file, delete from downloads for now
				$check = $this->remove($fileName);
				if (true == $check) { echo "File removed from queue.<br/>\n"; }
				else { echo "File could not be removed from queue.<br/>\n"; }
			}

			echo "KLF not loaded...<br/>";
			return false;
		}

		echo "KLF manifest loaded for $fileName ...<br/>\n";
		$check = $klf->saveMetaXml();
		if (true == $check) { echo "Saved manifest to transfers directory...<br/>"; }
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	push manifest to a peer
	//----------------------------------------------------------------------------------------------

	function pushManifest($fileName) {
		if ('' == $this->peerUID) { return false; }
		$peer = new P2P_Peer($this->peerUID);
		if (false == $peer->loaded) { return false; }
		echo "Pushing manifest to peer " . $peer->url . "p2p/givemanifest/ ...<br/>\n";
		$xml = $peer->sendMessage('file', $fileName);
		echo "<textarea rows='10' style='width:100%;'>$xml</textarea>\n";
		$klf = new KLargeFile($fileName);
		$klf->loadMetaXml($xml);
		if (false == $klf->loaded) { echo "KLF not loaded...<br/>"; return false; }
		echo "KLF manifest loaded for $fileName ...<br/>\n";
		$check = $klf->saveMetaXml();
		if (true == $check) { echo "Saved manifest to transfers directory...<br/>"; }
		return $check;		
	}

	//----------------------------------------------------------------------------------------------
	//.	pull a file part from this peer
	//----------------------------------------------------------------------------------------------
	//returns: true on sucess, false on failure [bool]

	function pullFilePart($fileName, $index) {
		global $kapenta;
		
		$part64 = '';									//%	base64 encoded part [string]
		$partBin = '';									//%	binary part [string]
		$peer = new P2P_Peer($this->peerUID);			//%	[object]
		$klf = new KLargeFile($fileName);				//% [object]

		if (false == $peer->loaded) { return false; }
		if (false == $klf->loaded) { return false; }
		echo "Pulling file part $fileName::$index ... loaded peer and KLF<br/>\n";

		foreach($klf->parts as $idx => $part) {
			if ($part['index'] == $index) { 
				$message = ''
				 . "<part>\n"
				 . "\t<path>$fileName</path>\n"
				 . "\t<hash>" . $part['hash'] . "</hash>\n"
				 . "\t<size>" . $part['size'] . "</size>\n"
				 . "\t<index>$index</index>\n"
				 . "</part>\n";

				$part64 = $peer->sendMessage('filepart', $message);					
	
				//----------------------------------------------------------------------------------
				//	handle and errors reported by the peer
				//----------------------------------------------------------------------------------
				if (false !== strpos($part64, '<error>')) {
					echo "<textarea rows='5' cols='80'>$part64</textarea><br/>\n";

					if (false !== strpos($part64, 'File not found.')) { 
						echo "File no longer available from peer, remove from downloads.<br/>\n";
						$check = $this->remove($fileName);
						if (true == $check) { echo "removed: $fileName <br/>\n"; }
						else { echo "could not remove: $fileName <br/>\n"; }						
					}

					return false;
				}

				//----------------------------------------------------------------------------------
				//	check the recieved part against hash
				//----------------------------------------------------------------------------------
				$partBin = base64_decode($part64);
				$partHash = sha1($partBin);
				echo "Part Hash: " . $part['hash'] . "<br/>";
				echo "Hash of recieved part: $partHash<br/>";

				if ($partHash != $part['hash']) { echo "Hash mismatch.<br/>"; return false; }

				$kapenta->fs->put($part['fileName'], $part64, true, true);
				echo "SAVED FILE: " . $part['fileName'] . "<br/>\n";

				$klf->parts[$idx]['status'] = 'ok';
				$klf->saveMetaXml();
				echo "SAVED FILE META: " . $part['fileName'] . "<br/>\n";

				return true;
			}
		}

		echo "Part not found: $index <br/>";
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	push a file part to a peer
	//----------------------------------------------------------------------------------------------
	//arg: fileName - location relative to installPath [string]
	//arg: index - part number [int]
	//returns: true on success, false on failure [bool]

	function pushFilePart($fileName, $index) {
		global $kapenta;
		$size = 0;								//%	raw size of this part [int]
		$hash = '';								//%	sha1 hash of raw part [string]
		$content = '';							//%	raw file chunk [string]
		$content64 = '';						//%	base64_encoded file chunk [string]

		echo "Sending file part $index of $fileName <br/>";

		$klf = new KLargeFile($fileName);
		$klf->makeFromFile($fileName);
		if (false == $klf->loaded) { return false; }

		echo "Loading Peer " . $this->peerUID . "<br/>";
		$peer = new P2P_Peer($this->peerUID);
		if (false == $peer->loaded) { return false; }
		echo "...loaded.<br/>";

		foreach($klf->parts as $part) {
			if ($part['index'] == $index) {
				$size = (int)$part['size'];
				$hash = $part['hash'];
				$content = $klf->getPart($index);
				$content64 = base64_encode($content);
				echo "Part $index found (size: $size)...<br/>\n";
			}
		}

		if (0 == $size) { return false; }

		$message = '' 
		 . "<part>\n"
		 . "\t<path>" . $fileName . "</path>\n"
		 . "\t<index>" . $index . "</index>\n"
		 . "\t<size>" . $size . "</size>\n"
		 . "\t<hash>" . $hash . "</hash>\n"
		 . "\t<content64>" . $content64 . "</content64>\n"
		 . "</part>\n";

		//echo "<textarea rows='10' style='width:100%;'>$message</textarea>";
		echo "Sending " . strlen($message) . " bytes<br/>";

		$response = $peer->sendMessage('givefilepart', $message);

		echo "<b>p2p/givefilepart/</b> responds<br/>";
		echo "<textarea rows='10' style='width:100%;'>$response</textarea>";
		if (false !== strpos($response, '</error>')) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	export this list as xml for peers
	//----------------------------------------------------------------------------------------------
	//returns: list of downloads serialized as XML [string]

	function toXml() {
		$xml = "<downloads>\n";					//%	return value [string]
		$fragments = array();					//%	individual entries [array]
		$sizes = array();						//%	for sorting [array]

		foreach($this->members as $fileName) {
			$manifest = 'no';
			$parts = '';	
			$size = 0;
			$hash = 0;

			if (true == $this->hasManifest($fileName)) {
				$manifest = 'yes';
				$outstanding = array();
				$klf = new KLargeFile($fileName);

				if (true == $klf->loaded) {
					$size = $klf->size;		
					foreach($klf->parts as $part) {
						if ('ok' != $part['status']) { $outstanding[] = $part['index']; }
					}
				}

				$parts = implode('|', $outstanding);
			}

			$sizes[$fileName] = (int)$size;			

			$fragments[$fileName] = ''
			 . "\t<download>\n"
			 . "\t\t<fileName>$fileName</fileName>\n"
			 . "\t\t<manifest>$manifest</manifest>\n"
			 . "\t\t<parts>$parts</parts>\n"
			 . "\t</download>\n";
		}

		asort($sizes);
		foreach($sizes as $fileName => $size) { $xml .= $fragments[$fileName]; }
		$xml .= "</downloads>";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand a download list recieved from a peer
	//----------------------------------------------------------------------------------------------
	//arg: xml - output of downloads->toXml() above [string]

	function expandXml($xml) {
		$downloads = array();					//%	return value [array]
		$xd = new KXmlDocument($xml);
		$kids = $xd->getChildren(1);
		if (true == is_array($kids)) {
			foreach ($kids as $childId) { $downloads[] = $xd->getChildren2d($childId); }
		}
		return $downloads;
	}

}

?>
