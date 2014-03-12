<?

	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//*	object for processing sets of offers from a peer
//--------------------------------------------------------------------------------------------------

class P2P_Offers {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;				//_	array of offers from a peer [array]
	var $loaded = false;		//_	set to true when members loaded [bool]
	var $peerUID = '';			//_	UID of a P2P_Peer object [string]
	var $maxGifts = 20;			//_	maximum number of offers to send at once [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function P2P_Offers($peerUID = '', $xml = '') {
		global $kapenta;

		$this->maxGifts = (int)$kapenta->registry->get('p2p.batchsize');
		if (0 == $this->maxGifts) { $this->maxGifts = 20; }

		$this->members = array();
		$this->peerUID = $peerUID;
		if ('' != $xml) { $this->loadXml($xml); }		
	}

	//----------------------------------------------------------------------------------------------
	//.	load a set of gifts from the database
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load($type) {
		global $kapenta;
		if ('' == $this->peerUID) { return false; }

		$this->members = array();

		//------------------------------------------------------------------------------------------
		//	exclude revisions if there is other stuff waiting
		//------------------------------------------------------------------------------------------

		$conditions = array();
		$conditions[0] = "peer='" . $kapenta->db->addMarkup($this->peerUID) . "'";
		$conditions[1] = "(status='waiting' OR status='want' OR status='')";
		$conditions[2] = "refModel != '" . $kapenta->db->addMarkup('revisions_revision') . "'";
		if ('files' == $type) { $conditions[3] = "type='file'"; }
		if ('objects' == $type) { $conditions[3] = "type='object'"; }

		$totalItems = $kapenta->db->countRange('p2p_gift', $conditions);

		if (0 == $totalItems) { $conditions[2] = '1=1'; }	//	nothing else waiting, send revisions

		$ob = "refModel='" . $kapenta->db->addMarkup('aliases_alias') . "', createdOn";	//	aliases first
		$range = $kapenta->db->loadRange('p2p_gift', '*', $conditions, $ob, $this->maxGifts);

		$this->members = $range;
		$this->loaded = true;

		$this->checkGifts();

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand a set of offers serialized as XML
	//----------------------------------------------------------------------------------------------
	//arg: xml - list of offers serialized as xml [string]

	function loadXml($xml) {
		$this->members = array();
		$xd = new KXmlDocument($xml);
		$children = $xd->getChildren();			// children of root node [array]
		foreach($children as $childId) { $this->members[] = $xd->getChildren2d($childId); }
		//foreach($this->members as $idx => $a) { $this->members[$idx]['response'] = ''; }
	}

	//----------------------------------------------------------------------------------------------
	//.	perform some quick sanity checks on the loaded gift set
	//----------------------------------------------------------------------------------------------
	//returns: true if everything checks out, false if changed / should be reloaded [bool]

	function checkGifts() {
		global $kapenta;
		$allOk = true;
		foreach($this->members as $item) {	
			//--------------------------------------------------------------------------------------
			//	if offering a file, make sure we have that file and its owner
			//--------------------------------------------------------------------------------------
			if ('file' == $item['type']) {
				if (false == $kapenta->fs->exists($item['fileName'])) { 
					$this->updateFile($item['refModel'], $item['refUID'], $item['fileName']);
					$allOk = false;
				}

				$owner = $kapenta->fileOwner($item['fileName']);
				if (0 == count($owner)) {
					$this->updateFile($item['refModel'], $item['refUID'], $item['fileName']);
					$allOk = false;
				}
			}

			// ...add more checks here

		}
		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	evaluate a set of offers, decide which of them we want
	//----------------------------------------------------------------------------------------------

	function evaluate() {
		global $kapenta;
		global $kapenta;
		global $revisions;	

		$dnset = new P2P_Downloads($this->peerUID);

		foreach($this->members as $idx => $offer) {
			$this->members[$idx]['status'] = '';
			$offer['status'] = '';

			//--------------------------------------------------------------------------------------
			//	decide if we want an object on offer
			//--------------------------------------------------------------------------------------
			if ('object' == $offer['type']) {				
				$item = $kapenta->db->getObject($offer['refModel'], $offer['refUID']);
				if (0 == count($item)) {

					if (
						(true == $kapenta->db->tableExists($offer['refModel'])) &&
						(false == $revisions->isDeleted($offer['refModel'], $offer['refUID']))
					) {
						$this->members[$idx]['status'] = 'want';
					} else {
						$this->members[$idx]['status'] = 'dnw';
					}

				}
				else {
					$xml = $kapenta->db->getObjectXml($offer['refModel'], $offer['refUID']);
					$hash = sha1($xml);

					$localTime = $kapenta->strtotime($item['editedOn']);
					$updateTime = $kapenta->strtotime($offer['updated']);

					if ($updateTime > $localTime) { $this->members[$idx]['status'] = 'want'; }
					if ($updateTime == $localTime) { 
						if ($hash != $offer['hash']) {
							$this->members[$idx]['status'] = 'want';
						} else {
							$this->members[$idx]['status'] = 'has';
						}
					}
					if ($updateTime < $localTime) { $this->members[$idx]['status'] = 'has'; }
				}

			}

			//--------------------------------------------------------------------------------------
			//	decide if we want a file
			//--------------------------------------------------------------------------------------
			if ('file' == $offer['type']) {
				if (false == $kapenta->fs->exists($offer['fileName'])) {
					// we don't have it, and we want it
					$this->members[$idx]['status'] = 'want';
					$check = $dnset->add($offer['fileName']);

				} else {
					// if we have this file, check the hash, we may want to redownload
					//TODO: check hash when more stable, to correct broken downloads, etc
					$this->members[$idx]['status'] = 'has';
				}
			}

			if ('' == $this->members[$idx]['status']) { $this->members[$idx]['status'] = 'dnw'; }
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	get the UID of a gift corresponding to an object
	//----------------------------------------------------------------------------------------------
	//arg: type - type of gift (file|object) [string]
	//arg: refModel - type of kapenta object / name of database table [string]
	//arg: refUID - UID of object being gifted [string]
	//opt: filename - location of a file relative to installPath [string]
	//returns: UID of P2P_Gift object on success, empty string on failure [string]

	function getGiftUID($type, $refModel, $refUID, $fileName = '') {
		global $kapenta;
		$conditions = array();
		$conditions[] = "type='" . $kapenta->db->addMarkup($type) . "'";
		$conditions[] = "peer='" . $kapenta->db->addMarkup($this->peerUID) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

		//NOTE: for now only one attached file per object
		//if ('file' == $type) { $conditions[] = "fileName='" . $kapenta->db->addMarkup($fileName) . "'"; }

		$range = $kapenta->db->loadRange('p2p_gift', '*', $conditions);
		foreach($range as $item) { return $item['UID']; }
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if an object is shared
	//----------------------------------------------------------------------------------------------
	//arg: model - name of database table / kapenta object type [string]
	//arg: UID - UID of object [string]
	//arg: properties - keys and values [dict]

	function isShared($model, $UID, $properties) {
		global $kapenta;

		// everything is shared by default
		$shared = true;
	
		// sharing may be explicitly enabled or diabled
		if (true == array_key_exists('shared', $properties)) {
			if ('yes' != $properties['shared']) { return false; }
		}

		// sharing may be inherited from a parent object, it it has one
		if (
			(true == array_key_exists('refModule', $properties)) &&
			(true == array_key_exists('refModel', $properties)) &&
			(true == array_key_exists('refUID', $properties))
		) {
			$parent = $kapenta->db->getObject($properties['refModel'], $properties['refUID']);
			if (0 != count($parent)) {
				$shared = $this->isShared($properties['refModel'], $properties['refUID'], $parent);
			}
		}

		return $shared;
	}

	//----------------------------------------------------------------------------------------------
	//.	search database for things we can give to this peer
	//----------------------------------------------------------------------------------------------
	//opt: print - print progress directly to browser [bool]
	//returns: number of gifts created / fixed [int]

	function scanDb($print = false) {
		global $kapenta;
		global $session;
		global $revisions;
		$count = 0;												//%	return value [string]

		$peer = new P2P_Peer($this->peerUID);
		if (false == $peer->loaded) { return $count; }
		if (true == $print) { echo "<h1>Scanning database for: " . $peer->name . "</h1>\n"; }

		//------------------------------------------------------------------------------------------
		//	get list of tables
		//------------------------------------------------------------------------------------------
		$tables = array();
		$allTables = $kapenta->db->loadTables();
		foreach($allTables as $table) {
			if (('p2p_gift' != $table) && ('wiki_mwimport' != $table)) { $tables[] = $table; }
		}

		//------------------------------------------------------------------------------------------
		//	check all items in this table
		//------------------------------------------------------------------------------------------
		foreach($tables as $table) {
			$dbSchema = $kapenta->db->getSchema($table);					//% db table definition [array]
			$lf = 100;											//%	line counter [int]
			$sql = "select * from " . strtolower($table);		//%	everything in table [string]
			$so = '';											//%	filter to shared items [string]

			if (true == array_key_exists('shared', $dbSchema['fields'])) {
				$so = " where shared='yes'";
			}

			$result = $kapenta->db->query($sql);							//%	recordset handle [int]

			if (true == $print) { echo "<h2>Searching table: $table</h2>\n"; }
			if (true == $print) { echo "Query: " . $sql . "<br/>\n"; }

			while($row = $kapenta->db->fetchAssoc($result)) {
				$item = $kapenta->db->rmArray($row);						//%	clean of db markup [dict]
				$add = true;									//%	not everything is added [bool]

				if (true == $print) {
					echo "."; flush(); $lf--;
					if (0 >= $lf) { $lf = 100; echo "<br/>\n"; }
				}

				if (true == $revisions->isDeleted($table, $item['UID'])) { $add = false; }
				if (false == $this->isShared($table, $item['UID'], $item)) { $add = false; }

				if (true == $add) {
					//------------------------------------------------------------------------------
					//	check/update extant gift, make a new one if not found
					//------------------------------------------------------------------------------
					$check = $this->updateObject($table, $item['UID'], $item);
					if (true == $check) { 
						$count++; 
						if (true == $print) { echo "U"; flush(); $lf--; }
					} else {
						if (true == $print) { echo "*"; flush(); $lf--; }
					}

				} else {
					//------------------------------------------------------------------------------
					//	item should not be in gifts table, make sure of that it is not
					//------------------------------------------------------------------------------
					$giftUID = $this->getGiftUID('object', $table, $item['UID']);
					if ('' != $giftUID) {
						$model = new P2P_Gift($giftUID);
						$model->delete();
						if (true == $print) { echo "X"; flush(); $lf--; }
					} else {
						if (true == $print) { echo "|"; flush(); $lf--; }
					}
				}

			} // end while in recordset
			if (true == $print) { echo "<br/><br/>\n"; }

		} // end for each table

		return $count;
	} 

	//----------------------------------------------------------------------------------------------
	//.	ask modules about files we can send to this peer
	//----------------------------------------------------------------------------------------------
	//;	Note: format for listfiles CSV lines is: refModule, refModel, refUID, fileName, hash
	//opt: print - set to true for noisy output [string]
	//returns: number of gifts created / fixed [int]

	function scanFiles($print = false) {
		global $kapenta;
		global $theme;

		$mods = $kapenta->listModules();		//%	all modules on this instance [array]
		$count = 0;								//%	return value [int]

		foreach($mods as $mod) {
			//TODO: page this to avoid memory issues when result sets get huge
			$block = "[[:$mod::listfiles::format=csv:]]";
			$list = $theme->expandBlocks($block, '');

			$lines = explode("\n", $list);
			foreach($lines as $line) {
				if ('' != trim($line)) {
					//------------------------------------------------------------------------------
					//	parse
					//------------------------------------------------------------------------------
					echo $line . "<br/>\n";
					$parts = explode(",", $line);
					$refModule = trim($parts[0]);
					$refModel = trim($parts[1]);		
					$refUID = trim($parts[2]);			
					$fileName = trim($parts[3]);		
					$hash = trim($parts[4]);		

					//------------------------------------------------------------------------------
					//	add to gifts
					//------------------------------------------------------------------------------
					$localHash = '';
					if (true == $kapenta->fs->exists($fileName)) {
						$localHash = sha1_file($kapenta->installPath . $fileName);
					}

					if (($localHash == $hash) && ('' != $fileName) && ('' != $hash)) {
						$giftUID = $this->getGiftUID('file', $refModel, $refUID, $fileName);
					
						$model = new P2P_Gift($giftUID);
						$model->peer = $this->peerUID;
						$model->type = 'file';
						$model->refModel = $refModel;
						$model->refUID = $refUID;
						$model->fileName = $fileName;
						$model->hash = $hash;
						$model->updated = $kapenta->datetime();
						$model->status = 'want';
						$model->save();
						$count++;
					}
				}
			} // end foreach line
		} // end foreach module

		return $count;
	}


	//----------------------------------------------------------------------------------------------
	//.	update an object item in the gifts table for this peer
	//----------------------------------------------------------------------------------------------
	//arg: model - name of a table / kapenta object type [string]
	//arg: UID - UID of an object [string]
	//arg: properties - set of keys and values that make up this object [dict]
	//returns: true if gift was updated, false if no change [bool]

	function updateObject($model, $UID, $properties) {
		global $kapenta;
		global $session;

		$check = false;											//%	return value [bool]
		$giftUID = $this->getGiftUID('object', $model, $UID);	//%	ref:P2P_Gift [string]	
		$xml = $kapenta->db->getObjectXml($model, $UID);					//% xml serialized object [string]
		$hash = sha1($xml);										//% sha1 hash of xml [string]

		if ('' != $giftUID) {
			//--------------------------------------------------------------------------------------
			//	update gift, assume all peers now want the changes
			//--------------------------------------------------------------------------------------
			$changed = false;
			$gift = new P2P_Gift($giftUID);

			if ($hash != $gift->hash) {	$changed = true; }
			if ($gift->updated != $properties['editedOn']) { $changed = true; }

			if (true == $changed) {
				$gift->updated = $properties['editedOn'];
				$gift->hash = $hash;
				$gift->status = 'want';
				$gift->shared = 'no';
				$report = $gift->save();
				if ('' == $report) { $check = true; }

			} else { /* $kapenta->session->msg('No changes to gift for: ' . $this->peerUID); */ }

		} else {
			//--------------------------------------------------------------------------------------
			//	new object, create new gift for this peer
			//--------------------------------------------------------------------------------------
			$gift = new P2P_Gift();

			$gift->peer = $this->peerUID;
			$gift->type = 'object';
			$gift->refModel = $model;
			$gift->refUID = $UID;
			$gift->fileName = '';
			$gift->hash = $hash;
			$gift->updated = $properties['editedOn'];
			$gift->status = 'want';
			$gift->shared = 'no';

			$report = $gift->save();
			if ('' == $report) { $check = true; }
			else { $kapenta->session->msg('Could not create gift for: ' . $this->peerUID); }

		}

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	update an file item in the gifts table for this peer
	//----------------------------------------------------------------------------------------------
	//arg: model - name of a table / kapenta object type [string]
	//arg: UID - UID of an object [string]
	//arg: fileName - location relative to installPath [string]
	//returns: true if gift was updated, false if no change [bool]

	function updateFile($model, $UID, $fileName) {
		global $kapenta;
		global $kapenta;
		global $session;

		//$kapenta->session->msg("p2p::offers::updateFile(model: $model, UID: $UID, file: $fileName)");

		$check = false;											//%	return value [bool]
		$giftUID = $this->getGiftUID('file', $model, $UID);		//%	ref:P2P_Gift [string]	
		$hash = $kapenta->fileSha1($fileName);					//% sha1 hash of xml [string]

		//------------------------------------------------------------------------------------------
		//	first make sure the file exists and can be hashed
		//------------------------------------------------------------------------------------------
		if (false == $kapenta->fs->exists($fileName)) {
			if ('' != $giftUID) {
				$model = new P2P_Gift($giftUID);		//	gift exists for non-existent file
				$check = $model->delete();				//	we can't share the file, so remove gift
				if (true == $check) { echo "<!-- deleted gift for file $fileName -->\n"; }
				if (false == $check) { echo "<!-- could not delete gift for file $fileName -->\n"; }
				return $check;
			}
		}

		if ('' == $hash) { $kapenta->session->msg("File cannot be hashed."); }
		if ('' == $hash) { return false; }						//	file could not be read

		//------------------------------------------------------------------------------------------
		//	then make sure that we know what owns this
		//------------------------------------------------------------------------------------------

		$owner = $kapenta->fileOwner($fileName);

		if (0 == count($owner)) {
			if ('' != $giftUID) {
				$model = new P2P_Gift($giftUID);		//	gift exists for non-existent owner
				$check = $model->delete();				//	we can't share the file, so remove gift
				if (true == $check) { echo "<!-- deleted gift for file $fileName -->\n"; }
				if (false == $check) { echo "<!-- could not delete gift for file $fileName -->\n"; }
				return $check;
			}
		}

		//------------------------------------------------------------------------------------------
		//	OK so far, create or update gift object
		//------------------------------------------------------------------------------------------

		if ('' != $giftUID) {
			//--------------------------------------------------------------------------------------
			//	update gift, assume all peers now want the changes
			//--------------------------------------------------------------------------------------
			//$kapenta->session->msg("p2p::offers::updateFile - Updating existing gift (UID: $giftUID).");
			$changed = false;
			$gift = new P2P_Gift($giftUID);
			if ($hash != $gift->hash) {	$changed = true; }

			if (true == $changed) {
				$gift->updated = $kapenta->datetime();
				$gift->hash = $hash;
				$gift->status = 'want';
				$gift->shared = 'no';
				$report = $gift->save();
				if ('' == $report) { $check = true; }
				//$kapenta->session->msg('Updated gift for: ' . $this->peerUID . ' (' . $fileName . ')');

			} else {
				//$kapenta->session->msg('No changes to gift for: ' . $this->peerUID);
			}

		} else {
			//--------------------------------------------------------------------------------------
			//	new object, create new gift for this peer
			//--------------------------------------------------------------------------------------
			//$kapenta->session->msgAdmin("p2p::offers::updateFile - Creating new gift.");
			$gift = new P2P_Gift();

			$gift->peer = $this->peerUID;
			$gift->type = 'file';
			$gift->refModel = $model;
			$gift->refUID = $UID;
			$gift->fileName = $fileName;
			$gift->hash = $hash;
			$gift->updated = $kapenta->datetime();
			$gift->status = 'want';
			$gift->shared = 'no';

			$report = $gift->save();
			if ('' == $report) { 
				//$kapenta->session->msg('Created new gift for: ' . $this->peerUID . ' (' . $fileName . ')');
				$check = true;

			} else { $kapenta->session->msg('Could not create gift for: ' . $this->peerUID); }

		}

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the status of some gift object as relates to this peer
	//----------------------------------------------------------------------------------------------
	//returns: status string if found, 'missing' string if not [string]

	function status($model, $UID) {
		global $kapenta;

		$conditions = array();
		$conditions[] = "peer='" . $kapenta->db->addMarkup($this->peerUID) . "'";
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($model) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($UID) . "'";

		$range = $kapenta->db->loadRange('p2p_gift', '*', $conditions);

		foreach($range as $item) { return $item['status']; }
		return 'missing';
	}

	//----------------------------------------------------------------------------------------------
	//.	make a note of responses to offer list from a peer
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of peer which responded [string]
	//returns: html report for debugging [string]

	function noteResponses() {
		$report = "<h2>Noting responses:</h2>\n";			//%	return value [string]

		foreach($this->members as $idx => $offer) {
			$report .= "response to offer ". $offer['UID'] ." is '". $offer['status'] ."'<br/>\n";
			$model = new P2P_Gift($offer['UID']);

			if (true == $model->loaded) {
				if ($model->status != $offer->status) {

					$model->status = $offer['status'];
					$err = $model->save();
					if ('' == $err) { $report .= "updated status...<br/>\n"; }
					else { $report .= "could not update status:<br/>$err<br/>\n"; }

				} else { $report .= "No change <br/>\n"; }
			} else { $report .= "could not load gift: " . $offer['UID'] . "<br/>\n"; }
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to XML for return to peer
	//----------------------------------------------------------------------------------------------

	function toXml() {
		global $kapenta;
		$xml = "<offers>\n";								//%	return value [string]
		foreach($this->members as $item) {
			$xml .= ''
			 . "\t<offer>\n"
			 . "\t\t<UID>" . $item['UID'] . "</UID>\n"
			 . "\t\t<type>" . $item['type'] . "</type>\n"
			 . "\t\t<refModel>" . $item['refModel'] . "</refModel>\n"
			 . "\t\t<refUID>" . $item['refUID'] . "</refUID>\n"
			 . "\t\t<fileName>" . $item['fileName'] . "</fileName>\n"
			 . "\t\t<hash>" . $item['hash'] . "</hash>\n"
			 . "\t\t<updated>" . $item['updated'] . "</updated>\n"
			 . "\t\t<status>" . $item['status'] . "</status>\n"
			 . "\t</offer>\n";
		}

		$xml .= "</offers>\n";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to HTML for display / debugging
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		global $theme;
		$html = '';					//%	return value [string]
		$table = array();
		$table[] = array('UID', 'Type', 'Reference', 'Hash', 'Updated', 'Status*');

		foreach($this->members as $idx => $a) {
			$ref = '';
			if ('file' == $a['type']) { $ref = $a['fileName']; }
			if ('object' == $a['type']) { $ref = $a['refModel'] . '::' . $a['refUID']; }
			$table[] = array(
				$a['UID'], $a['type'], $ref, $a['hash'], $a['updated'], $a['status']
			);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

}

?>
