<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	object which talks directly to another peer
//--------------------------------------------------------------------------------------------------

class P2P_Client {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	//TODO:
	var $peerUID = '';			//_	UID of the peer this client talks to [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: peerUID - UID of a P2P_Peer object [string]

	function P2P_Client($peerUID = '') {
		$this->peerUID = $peerUID;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if we are currently allowed to transfer files (fires usually sent at night)
	//----------------------------------------------------------------------------------------------
	//returns: true during file transfer hours, false if not [bool]

	function inFileHours() {
		global $registry;
		global $kapenta;
		$inHours = false;			//%	return value [bool]

		$fileHours = $registry->get('p2p.filehours');
		$hours = explode(",", $fileHours);
		$current = (int)date('G', $kapenta->time());

		foreach($hours as $hour) {
			if ($current == (int)$hour) { $inHours = true; }
		}

		return $inHours;
	}

	//----------------------------------------------------------------------------------------------
	//.	push gifts (objects) to another peer
	//----------------------------------------------------------------------------------------------
	//;	$this->peerUID should be set before this is called.

	function push() {
		global $db;
		global $theme;
		global $kapenta;			
		global $user;
		global $registry;

		$report = '';						//% return value [string]

		//return "Temporarily disabled.";

		if ('yes' != $registry->get('p2p.enabled')) { return 'P2P Disabled'; }
		$model = new P2P_Peer($this->peerUID);
		if (false == $model->loaded) { return 'Could not load P2P_Peer object.'; }

		//------------------------------------------------------------------------------------------
		//	make a list of offers and send it to the /giveoffers/ interface
		//------------------------------------------------------------------------------------------
		$set = new P2P_Offers($model->UID);
		$set->load('both');
		if (0 == count($set->members)) { return $report . "No items for this peer.<br/>"; }
		$message = $set->toXml();
		$report .= "<h3>sending: p2p/giveoffers/</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$message</textarea>";

		$response = $model->sendMessage('giveoffers', $message);

		$report .= "<h3>response: p2p/giveoffers/</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

		//------------------------------------------------------------------------------------------
		//	load the set returned by /giveoffers/ and collect the items this peer indicates it wants
		//------------------------------------------------------------------------------------------
		$newSet = new P2P_Offers($model->UID, $response);
		$report .= $newSet->toHtml();

		$objectsXml = '';

		$report .= "<h3>Checking response...</h3>\n";
		foreach($newSet->members as $idx => $item) {
			if ('want' == $item['status']) {
				if ('object' == $item['type']) {
					$report .= ''
					 . "Peer would like us to send: "
					 . $item['refModel'] . '::' . $item['refUID'] . "<br/>";

					$tempXml = $db->getObjectXml($item['refModel'], $item['refUID']);

					if ('' != $tempXml) {
						// item loaded and serialized
						$objectsXml .= $tempXml;
						$report .= "ADDING:<br/>" . htmlentities($tempXml) . " (" . $item['UID'] . ")<br/>";
					} else {
						// item could not ne loaded, delete the gift object
						$report .= "Could not load object of gift: " . $item['UID'] . "<br/>\n";
						if (false == $db->objectExists($item['refModel'], $item['refUID'])) {
							$rmGift = new P2p_Gift($item['UID']);
							$check = $rmGift->delete();
							if (true == $check) { $report .= "Gift removed.<br/>"; }
							else { $report .= "Could not delete gift: ". $item['UID'] ."<br/>\n"; }
						}
					}
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	send all objects the peer wants
		//------------------------------------------------------------------------------------------

		if ('' != $objectsXml) {
		
			$objectsXml = "<kobjects>\n" . $objectsXml . "</kobjects>\n";		

			$report .= "<h3>sending objects: p2p/giveobject/</h3>";
			$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$objectsXml</textarea>";

			$response = $model->sendMessage('giveobject', $objectsXml);

			$report .= "<h3>peer responds:</h3>";
			$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

			//--------------------------------------------------------------------------------------
			//	interpret this response
			//--------------------------------------------------------------------------------------
			$xdty = new KXmlDocument($response);
			$children = $xdty->getChildren();
			foreach($children as $childId) {
				$ty = $xdty->getChildren2d($childId);
				$report .= ''
				 . "Response to: " . $ty['model'] . '::' . $ty['UID']
				 . ' was ' . $ty['status'] . "<br/>";

				foreach($newSet->members as $idx => $item) {
					if (($item['refModel'] == $ty['model']) && ($item['refUID'] == $ty['UID'])) {
						$report .= "Updated set index $idx<br/>\n";
						$newSet->members[$idx]['status'] = $ty['status'];
					}
				}

			}

		} else {
			$report .= "<h3>Peer does not want any of the offered objects.</h3>";
			$report .= "<textarea rows='10' style='width: 100%'>" . $response . "</textarea><br/>\n";
		}

		//------------------------------------------------------------------------------------------
		//	update P2P_Gifts table based on responses
		//------------------------------------------------------------------------------------------

		$report .= "<h3>Updating local gifts table.</h3>";	

		foreach($newSet->members as $idx => $item) {
			$model = new P2P_Gift($item['UID']);
			if (true == $model->loaded) {			
				if ($model->status != $item['status']) {
					$model->status = $item['status'];
					$model->save();
					$report .= ''
					 . "updated gift " . $item['UID'] . ": " . $item['status']
					 . " (" . $item['refModel'] . '::' . $item['refUID'] . ")<br/>\n";

				} else {
					$report .= "gift " . $item['UID'] . " statuses match, unchanged<br/>\n";
				}
			} else {
				$report .= "could not load P2P_Gift object: " . $item['UID'] . "<br/>\n";
			}
		}

		//------------------------------------------------------------------------------------------
		//	send all files the peer wants
		//------------------------------------------------------------------------------------------
		//TODO: this

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	pull gifts (objects) from another peer
	//----------------------------------------------------------------------------------------------
	//;	$this->peerUID should be set before this is called.

	function pull() {
		global $db;
		global $theme;
		global $kapenta;			
		global $user;
		global $registry;

		$report = '';								//%	return value [string]

		if ('yes' != $registry->get('p2p.enabled')) { return 'P2P Disabled'; }
		$model = new P2P_Peer($this->peerUID);
		if (false == $model->loaded) { return 'Could not load P2P_Peer object.<br/>'; }

		//------------------------------------------------------------------------------------------
		//	send the message (server returns <ok/> if trusted
		//------------------------------------------------------------------------------------------
		$xml = $model->sendMessage('offers', 'both|' . $kapenta->time());

		//$report .= $theme->expandBlocks('[[:theme::ifscrollheader:]]');
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$xml</textarea>";

		$report .= "<h2>Offers</h2>";
		$set = new P2P_Offers($model->UID, $xml);
		if (0 == count($set->members)) { return $report . "Peer reports no changes.<br/>\n"; }

		$report .= "<h3>Before evaluation</h3>\n";
		$report .= $set->toHtml();

		$set->evaluate();

		$report .= "<h3>After evaluation</h3>\n";
		$report .= $set->toHtml();

		//------------------------------------------------------------------------------------------
		//	request any objects we want
		//------------------------------------------------------------------------------------------
		$report .= "<h2>Downloading objects we want</h2>\n";

		$message = "<requests>\n";
		foreach($set->members as $item) {
			if (('want' == $item['status']) && ('object' == $item['type'])) { 
				$message .= "\t<request>\n";
				$message .= "\t\t<model>" . $item['refModel'] . "</model>\n";
				$message .= "\t\t<UID>" . $item['refUID'] . "</UID>\n";
				$message .= "\t\t<updated>" . $item['updated'] . "</updated>\n";
				$message .= "\t\t<hash>" . $item['hash'] . "</hash>\n";
				$message .= "\t</request>\n";
			}
		}
		$message .= "</requests>";

		$report .= "<h3>sending: p2p/object/</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$message</textarea>";

		$response = $model->sendMessage('object', $message);
		$report .= "<h3>response:</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

		//------------------------------------------------------------------------------------------
		//	save them to the database
		//------------------------------------------------------------------------------------------

		$xdo = new KXmlDocument($response);
		$children = $xdo->getChildren();
		foreach ($children as $childId) {
			$kobjXml = $xdo->getInnerXml($childId, true);
			$report .= "<h3>object:</h3>";
			$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$kobjXml</textarea>";
	
			$check = $db->storeObjectXml($kobjXml, false, false, false);
			if (true == $check) { 
				$report .= "<b>OBJECT STORED</b><br/>"; 
				$kObjAry = $db->objectXmlToArray($kobjXml);
				$kUID = $kObjAry['fields']['UID'];
				foreach ($set->members as $idx => $offer) {
					if (($offer['UID'] == $kUID) && ($offer['type'] == 'object')) {
						//--------------------------------------------------------------------------
						//	set gift status for peer and raise object_received event
						//--------------------------------------------------------------------------
						$report .= "Updating response to: " . $kUID . " ($idx)<br/>";
						$set->members[$idx]['status'] = 'has';
						
						$args = array(
							'type' => 'object',
							'model' => $kObjAry['model'],
							'UID' => $kObjAry['fields']['UID'],
							'properties' => $kObjAry['fields'],
							'peer' => $this->peerUID
						);

						$objRef = $kObjAry['model'] . '::' . $kObjAry['fields']['UID'];
						$kapenta->logP2P('received object (pull) ' . $objRef);
						$kapenta->raiseEvent('*', 'object_received', $args);
					}
				}

			} else {	
				$report .= ''
				 . "<b>object not stored, database error</b><br/>\n"
				 . $db->lasterr;
			}
		}

		//------------------------------------------------------------------------------------------
		//	update peer about this
		//------------------------------------------------------------------------------------------
		$report .= "<h2>Telling peer about it</h2>\n";
		$report .= "<h3>sending: p2p/bytheway/</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>" . $set->toXml() . "</textarea>";
		$btwresponse = $model->sendMessage('bytheway', $set->toXml());
		$report .= "<h3>response:</h3>";
		$report .= "<textarea rows='10' cols='80' style='width: 100%;'>$btwresponse</textarea>";
	
		//------------------------------------------------------------------------------------------
		//	done..
		//------------------------------------------------------------------------------------------
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	get manifests and start files downloading
	//----------------------------------------------------------------------------------------------
	//returns: HTML report of any actions taken [string]

	function pullFiles() {
		global $registry;
		global $kapenta;

		$report = '';												//%	return value [string]
		$remaining = (int)$registry->get('p2p.batchparts');			//% bandwidth limit [int]

		if ('yes' != $registry->get('p2p.enabled')) { return 'P2P Disabled'; }
		if (false == $this->inFileHours()) { return 'Outside of file transfer hours.'; }

		$downloads = new P2P_Downloads($this->peerUID);
		if (0 == count($downloads->members)) { return $report . 'No downloads in progress.'; }

		foreach($downloads->members as $fileName) {
			//--------------------------------------------------------------------------------------
			//	first get the manifest
			//--------------------------------------------------------------------------------------
			if (false == $downloads->hasManifest($fileName)) {
				$report .= "<b>Need manifest for file:</b> $fileName <br/>";
				$check = $downloads->pullManifest($fileName);

				if (true == $check) { $report .= "... manifest pulled from peer<br/>"; }
				else { $report .= "... manifest could not be downloaded.<br/>"; }
			}

			//--------------------------------------------------------------------------------------
			//	then download n parts of the file
			//--------------------------------------------------------------------------------------
			if (true == $downloads->hasManifest($fileName)) {

				$klf = new KLargeFile($fileName);
				if (true == $klf->loaded) { 
					$allOk = true;						//%	stays true if all parts download [bool]

					//------------------------------------------------------------------------------
					//	download some part
					//------------------------------------------------------------------------------
					foreach($klf->parts as $idx => $part) {
						if ('pending' == $part['status']) {
							if ($remaining > 0) {
								$report .= "Loading part $idx of $fileName<br/>\n";
								$report .= "Part file: " . $part['fileName'] . "<br/>\n";
								$check = $downloads->pullFilePart($fileName, $part['index']);
								if (false == $check) { $allOk = false; }
								$remaining--;

							} else { $allOk = false; }
						}
					}

					//------------------------------------------------------------------------------
					//	stitch together if all parts are down
					//------------------------------------------------------------------------------
					if (true == $allOk) {
						$check = $klf->stitchTogether();
						if (true == $check) {
							//----------------------------------------------------------------------
							//	we now have the file :-) delete the meta and remove from downloads
							//----------------------------------------------------------------------
							$report .= "File stitched together... removing manifest<br/>\n";
							$downloads->remove($fileName);
							$report .= "File stitched together... removing from downloads<br/>\n";
							$klf->delete();

							//----------------------------------------------------------------------
							//	note it in the log
							//----------------------------------------------------------------------
							$msg = "Completed download of file: " . $klf->path  . "<br/>\n";

							//----------------------------------------------------------------------
							//	raise an event so the module can claim it
							//----------------------------------------------------------------------
							$args = array('fileName' => $klf->path);
							$kapenta->raiseEvent('*', 'p2p_filedone', $args);

							//----------------------------------------------------------------------
							//	raise an event on receipt of file (triggers sharing with others)
							//----------------------------------------------------------------------
							$msg = "Re-sharing with peers...<br/>\n" . $klf->path;

							$args = array(
								'module' => $klf->module,
								'model' => $klf->model,
								'UID' => $klf->UID,
								'fileName' => $klf->path,
								'peer' => $this->peerUID
							);
							$kapenta->raiseEvent('*', 'file_received', $args);

						} else {
							//----------------------------------------------------------------------
							//	remove this download and try again
							//----------------------------------------------------------------------
							$report .= "<b>Error:</b> Could not stitch together...<br/>";
							$downloads->remove($fileName);
							$klf->delete();
						}
					}

				} // end if loaded
			} // end if hasManifest


		}

		if ('' == $report) { $report .= "<div class='inlinequote'>No files to download.</div>"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	offer and send files from behind a firewall
	//----------------------------------------------------------------------------------------------
	//;	$this->peerUID should be set before this is called.
	//returns: HTML report of any actions taken [string]

	function pushFiles() {
		global $kapenta;
		global $registry;	

		$report = '';												//%	return value [string]
		$remaining = (int)$registry->get('p2p.batchparts');			//% bandwidth limit [int]

		if ('yes' != $registry->get('p2p.enabled')) { return 'P2P Disabled'; }
		if (false == $this->inFileHours()) { return 'Outside of file transfer hours.'; }

		//------------------------------------------------------------------------------------------
		//	get list of files this peer is downloading
		//------------------------------------------------------------------------------------------
		
		$peer = new P2p_Peer($this->peerUID);
		if (false == $peer->loaded) { return '<b>Error:</b> Peer not found.'; }

		$xml = $peer->sendMessage('downloads', $kapenta->time());
		$report .= "<b>Download list:</b><br/>\n";
		$report .= "<textarea rows='10' style='width:100%;'>$xml</textarea><br/>\n";

		$downloads = new P2P_Downloads($peer->UID);
		$meta = $downloads->expandXml($xml);

		//------------------------------------------------------------------------------------------
		//	send manifests and parts
		//------------------------------------------------------------------------------------------
		foreach ($meta as $dn) {
			$report .= ''
			 . "<h3>fileName: " . $dn['fileName'] . "</h3>\n"
			 . "manifest: " . $dn['manifest'] . "<br/>\n"
			 . "parts: " . $dn['parts'] . "<br/>\n"
			 . "<br/>\n";

			//--------------------------------------------------------------------------------------
			//	if peer does not have manifest, send manifest
			//--------------------------------------------------------------------------------------
			if (('no' == $dn['manifest']) && (true == $kapenta->fileExists($dn['fileName']))) {
				$report .= "<b>Sending Manifest for:</b> " . $dn['fileName'] . "<br/>";
				$klf = new KLargeFile($dn['fileName']);
				$klf->makeFromFile();

				if (true == $klf->loaded) {
					foreach($klf->parts as $idx => $part) { $klf->parts[$idx]['status'] = 'pending'; }
					$manifest = $klf->toXml();
					$report .= "<textarea rows='10' style='width:100%;'>$manifest</textarea><br/>\n";

					$result = $peer->sendMessage('givemanifest', $manifest);
					$report .= "<b>p2p/givemanifest/</b> responds<br/>\n";
					$report .= "<textarea rows='10' style='width:100%;'>$result</textarea><br/>\n";	
				} else {
					$report .= "<b>Could not create manifest for this file.</b><br/>\n";
				}
			}

			//--------------------------------------------------------------------------------------
			//	for all the parts they do not have, send parts
			//--------------------------------------------------------------------------------------
			if ('' != $dn['parts']) {
				$parts = explode("|", $dn['parts']);
				//shuffle parts

				shuffle($parts);

				foreach($parts as $part) {
					if ($remaining > 0) {
						$report .= "<h3>Sending part $part of " . $dn['fileName'] . "</h3>\n";
						$check = $downloads->pushFilePart($dn['fileName'], (int)$part);
						if (true == $check) { $report .= "...sent<br/>\n"; }
						else { $report .= "...ERROR, part not sent.<br/>\n"; }
						$remaining--;
					}
				}
			}
		} // end foreach download

		return $report;
	}

}

?>
