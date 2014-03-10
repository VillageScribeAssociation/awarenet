<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/bzutils.inc.php');

//--------------------------------------------------------------------------------------------------
//*	Object to queue updates for other peers
//--------------------------------------------------------------------------------------------------
//+	Buffered updates for other peers are stroed in a series of small files on disk.  The file is 
//+	written until it cannot hold any futher updates, and is then locked and compressed.
//+
//+	Multiple output queues are kept for each peer accoring to message priority:
//+
//+		0	Reserved / RPC
//+		1	Software update notifications
//+		3	High priority items - alias, school and user objects, (un)deletion notifications
//+		5	Ordinary priority - most database objects and file notifications
//+		8	Low priority - revisions, notifcation indexes
//+
//+	File name format:	
//+
//+		          5.34258907343.954632479248.9345982348943.xml.bz2
//+		| priority | timestamp | peerUID    | random UID  |fmt|ext
//+
//+	File extension many be "bz2" for bzipped files, or "txt" if compression is not available.  Open
//+	files always have the extension "txt".
//+
//+	Files are locked for writing by creating a separate lock file, with the same name as the buffer
//+	file and the extension "lock".
//+	
//+	The purpose of the random UID in filenames is to prevent information leackage to an attacker 
//+	guessing file names, and to allow multiple files to be written per second.

class P2P_Updates {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $peer = '';							//_	peer we are storing updates for [string]
	var $priority = '';						//_ priority of this queue (0-9) [string]

	var $maxFileSize = 524288;				//_	maximum size of buffer files, bytes [int]
	var $bufferDir = '';					//_	location to store outgoing updates [string]
	var $outFile = '';						//_	current output file for this peer [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: peer - UID of a p2p_peer object [string]

	function P2P_Updates($peer) {
		global $kapenta;

		$this->peer = $peer;

		if ('' != $peer) {
			$this->bufferDir = 'data/p2p/pending/' . $peer . '/';
			if (false == $kapenta->fs->exists($this->bufferDir)) {
				$kapenta->fileMakeSubdirs($this->bufferDir . 'new.txt', true);
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	list buffered updates for this peer
	//----------------------------------------------------------------------------------------------
	//returns: array of file names relative to installPath mapped to lock status [string]

	function listFiles() {
		global $kapenta;
		global $session;

		$files = array();					//%	return value [array]

		if (('' == $this->bufferDir) || (false == $kapenta->fs->exists($this->bufferDir))) {
			$session->msgAdmin('P2P output directory does not exist', 'bad');
			return $files;
		}

		$allFiles = $kapenta->fs->listDir($this->bufferDir);

		foreach($allFiles as $fileName) {
			if (false == strpos($fileName, '.lock')) {
				//----------------------------------------------------------------------------------
				//	not a lock file, check status
				//----------------------------------------------------------------------------------

				$parts = explode('.', basename($fileName));

				$locked = 'no';
				$lockFile = str_replace('.xml.bz2', '.xml.lock', $fileName);
				$lockFile = str_replace('.xml.txt', '.xml.lock', $fileName);
				if (true == in_array($lockFile, $allFiles)) { $locked = 'yes'; }

				$files[$fileName] = array(
					'priority' => $parts[0],
					'timestamp' => $parts[1],
					'peer' => $parts[2],
					'UID' => $parts[3],
					'format' => $parts[4],
					'ext' => $parts[5],
					'locked' => $locked
				);
			} else {
				//----------------------------------------------------------------------------------
				//	is a lock file, check if still needed
				//----------------------------------------------------------------------------------
				$txtFile = str_replace('.xml.lock', '.xml.txt', $fileName);
				$bz2File = str_replace('.xml.lock', '.xml.bz2', $fileName);

				if (
					(false == $kapenta->fs->exists($txtFile)) &&
					(false == $kapenta->fs->exists($bz2File))
				) {
					//echo "expired: $fileName<br/>\n";
					$kapenta->fileDelete($fileName);
				} else {
					//echo "lock active: $fileName<br/>\n";
				}

			}
		}

		ksort($files);
		//echo "<pre>";
		//print_r($files);
		//echo "</pre>\n";

		return $files;
	}

	//----------------------------------------------------------------------------------------------
	//.	get current buffer file for this peer - create one if none exist
	//----------------------------------------------------------------------------------------------
	//arg: priority - queue priority, 0-9 [string]
	//returns: name of file relative to installPath, empty string on failure [string]

	function getCurrentOutputFile($priority) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	first check cached value
		//------------------------------------------------------------------------------------------
		if ('' == $this->peer) { return ''; }
		if (('' != $this->outFile) && ($priority == $this->priority)) {
			//echo "queue already open: " . $this->peer . "!" . $priority . "<br/>\n";
			//echo "outfile alreadyset: " . $this->outFile . "\n";

			return $this->outFile;
		}
		
		//------------------------------------------------------------------------------------------
		//	check disk for an existing (not locked) file
		//------------------------------------------------------------------------------------------
		$files = $this->listFiles();
		foreach($files as $fileName => $meta) {
			if (('no' == $meta['locked']) && ($priority == $meta['priority'])) {
				//echo "found open queue: " . $this->peer . "!" . $priority . "<br/>\n";
				$this->priority = $priority;
				$this->outFile = $fileName;

				//echo "set outFile #1 (existing): " . $this->outFile . "\n";

				return $fileName;
			}
		}

		//------------------------------------------------------------------------------------------
		//	create a new output file
		//------------------------------------------------------------------------------------------
		//echo "No queue found for peer " . $this->peer . " pri: " . $priority . "<br/>\n";
		$this->priority = $priority;
		$this->outFile = ''
		 . $this->bufferDir
		 . $this->priority . '.'
		 . $kapenta->time() . '.'
		 . $this->peer . '.'
		 . $kapenta->createUID()
		 . '.xml.txt';

		//echo "set outFile #2 (new): " . $this->outFile . "\n";

		$header = ''
		 . "## file: " . $this->outFile . "\n"
		 . "## peer: " . $this->peer . "\n## created: " . $kapenta->datetime() . "\n";

		$check = $kapenta->fs->put($this->outFile, $header);
		if (false == $check) { return ''; }

		return $this->outFile;
	}

	//----------------------------------------------------------------------------------------------
	//.	lock a file
	//----------------------------------------------------------------------------------------------
	//arg: name - location of a buffer file, relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function lockFile($fileName) {
		global $kapenta;

		if (false == $kapenta->fs->exists($fileName)) { return false; }

		//------------------------------------------------------------------------------------------
		//	create the lock file
		//------------------------------------------------------------------------------------------
		$lockFile = str_replace('.txt', '.lock', $fileName);
		$check = $kapenta->fs->put($lockFile, $kapenta->dateTime());

		$this->outFile = '';			//	force new output file to be created

		//------------------------------------------------------------------------------------------
		//	compress the queue
		//------------------------------------------------------------------------------------------
		$raw = $kapenta->fs->get($fileName);
		$bzFile = str_replace('.txt', '.bz2', $fileName);
		$kapenta->fs->put($bzFile, p2p_bzcompress($raw));

		//------------------------------------------------------------------------------------------
		//	delete the text document
		//------------------------------------------------------------------------------------------
		$kapenta->fileDelete($fileName);

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a message for a peer
	//----------------------------------------------------------------------------------------------
	//arg: update - an XML document [string]
	//returns: true on success, false on failure [bool]

	function storeMessage($msg, $priority) {
		global $kapenta;

		//echo "storing message for peer " . $this->peer . "!" . $priority . "<br/>\n";

		if ($priority != $this->priority) { $this->outFile = ''; }

		if ('' == $this->peer) { return false; }
		if ('' == $this->bufferDir) { return false; }
		if ('' == $this->outFile) { $this->getCurrentOutputFile($priority); }
		if ('' == $this->outFile) { return false; }

		$size = $kapenta->fs->size($this->outFile);
		$msg = ''
		 . "## peer: " . $this->peer . "\n"
		 . "## priority: " . $priority . "\n"
		 . $msg
		 . "## --- \n";

		//------------------------------------------------------------------------------------------
		//	close the current output file if it is full
		//------------------------------------------------------------------------------------------
		if (($size + mb_strlen($msg, 'ASCII')) > $this->maxFileSize) {
			$locked = $this->lockFile($this->outFile);
			if (false == $locked) {
				$kapenta->logErr('p2p', 'P2P_Updates::storeMessage()', 'Could not lock file.');
				return false;
			}
			$this->getCurrentOutputFile($priority);
			if ('' == $this->outFile) {
				$kapenta->logErr('p2p', 'P2P_Updates::storeMessage()', 'Error creating outfile.');
				return false;
			}
		}

		//------------------------------------------------------------------------------------------
		//	add the update to the file
		//------------------------------------------------------------------------------------------
		$check = $kapenta->fs->put($this->outFile, $msg, true, false, 'a+');

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the prioirty of an object given its type
	//----------------------------------------------------------------------------------------------
	//arg: model - type of obejct stored in the database [string]
	//returns: 0-9 [string]

	function getPriority($model) {
		$priority = '5';
		switch($model) {
			case 'aliases_alias':				$priority = '2';	break;
			case 'users_user':					$priority = '2';	break;
			case 'users_role':					$priority = '2';	break;
			case 'tags_tag':					$priority = '2';	break;
			case 'revisions_revison':			$priority = '8';	break;
			case 'notifications_userindex':		$priority = '8';	break;
		}
		return $priority;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize and object for transport
	//----------------------------------------------------------------------------------------------

	function encodeDbObject($model, $data) {
		$fields = '';

		if (false == is_array($data)) { return ''; }

		foreach($data as $field => $value) {
			$fields .= '    ' . $field . ':' . base64_encode($value) . "\n";
		}

		$msg = ''
		 . "  <update>\n"
		 . "    <model>" . $model . "</model>\n"
		 . "    <fields>\n"
		 . $fields
		 . "    </fields>\n"
		 . "  </update>\n";

		return $msg;
	}

	//----------------------------------------------------------------------------------------------
	//.	explode received file into single events in ~/data/p2p/received/
	//----------------------------------------------------------------------------------------------
	//arg: updates - raw contents of an update file [string]
	//arg: peerUID - UID of the peer we received this from [string]
	//returns: number of updates saved [int]

	function explode($updates, $priority, $peerUID) {
		global $kapenta;

		echo "writing updates\n";

		$lines = explode("\n", $updates);
		$buffer = '';									//%	current XML document [string]
		$count = 0;										//%	return value [int]

		echo "lines: " . count($lines) . "\n";

		foreach($lines as $line) {
			if ((strlen($line) > 3) && ('## ' == substr($line, 0, 3))) {

				if ('' != trim($buffer)) {
					//------------------------------------------------------------------------------
					//	save the received update
					//------------------------------------------------------------------------------
    				list($usec, $sec) = explode(" ", microtime());
					$fileName = 'data/p2p/received/' . $priority . '_' . $peerUID . '_' . $sec . '_' . $usec . '.evt';
					$check = $kapenta->fs->put($fileName, $buffer);

					if (true == $check) {
						echo "Stored event: $fileName<br/>\n";
						$buffer = '';
						$count++;
					}

				} else { $buffer = ''; }

			} else { $buffer .= $line . "\n"; }
		}

		return $count;
	}

}

?>
