<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	returns an object to a peer which requests it
//--------------------------------------------------------------------------------------------------
//+
//+	Message should be an XML document representing the list of objects the client would like, eg:
//+
//+		<requests>
//+			<request>
//+				<model></model>
//+				<UID></UID>
//+				<updated></updated>
//+				<hash></hash>
//+			</request>
//+		</requests>
//+
//postarg: message - offer type and timestamp requested [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('message', $_POST)) { $page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $page->doXmlError('Peer UID not sent.'); }

	$model = new P2P_Peer($_POST['peer']);
	if (false == $model->loaded) { $page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $model->checkMessage($message, $signature)) { $page->doXmlError('Bad signaure.'); }

	//----------------------------------------------------------------------------------------------
	//	send the objects
	//----------------------------------------------------------------------------------------------
	echo "<kobjects>\n";
	$log = '';

	$xd = new KXmlDocument($message);
	$children = $xd->getChildren();			// children of root node [array]
	foreach($children as $childId) {
		$request = $xd->getChildren2d($childId);
		if ((true == array_key_exists('model', $request)) && (array_key_exists('UID', $request))) {
			$xml = $db->getObjectXml($request['model'], $request['UID']);
			$log .= "raw xml:<br/>\n$xml<br/>\n\n";
			$log .= "object xml: " . strlen($xml) . " bytes<br/>\n";
			$hash = sha1($xml);
			if ($hash == $request['hash']) { echo $xml; }
			else {
				//----------------------------------------------------------------------------------
				// hashes do not match, check that this object is correct in the gifts table
				//----------------------------------------------------------------------------------
				$log .= "WARNING: hash mismatch - $hash != " . $request['hash'] . "<br/>";
				$properties = $db->getObject($request['model'], $request['UID']);
				if (false == $properties) {				
					// no such objects, delete from gifts table
					$sql = "DELETE FROM p2p_gift"
					 . " WHERE refModel='" . $db->addMarkup($request['model']) . "' "
					 . "AND refUID='" . $db->addMarkup($request['UID']) . "'";
					$db->query($sql);
					//echo $sql . "<br/>\n";

				} else {
					// fix object in gifts table
					$offers = new P2P_Offers($model->UID);
					$check = $offers->updateObject($request['model'], $request['UID'], $properties);
					if (true == $check) { $log .= "Object updated.<br/>"; }
					if (false == $check) { $log .= "Object could not be updated.<br/>"; }
				}
			}
		}
	}
	
	echo "</kobjects>\n";
	echo $log;

?>
