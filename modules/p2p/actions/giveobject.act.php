<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	peer is giving a set of objects that we requested from them
//--------------------------------------------------------------------------------------------------
//+	Message from peer should be a signed list of objects, eg:
//+
//+		<kobjects>
//+			<kobject type='aliases_alias'>
//+				<UID>123456788</path>
//+				... rest of fields here ...
//+			</kobject>
//+		</kobjects>
//+
//+	Response to peer details the new state of these gifts, eg:
//+
//+		<thankyou>
//+			<object>
//+				<model>aliases_alias</model>
//+				<UID>123456788</UID>
//+				<status>has</status>
//+			</object>
//+		</thankyou>
//+
//+	Where status may be has, dnw or want (in the case of database failure)

//postarg: message - object serialized as XML [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if ('yes' != $kapenta->registry->get('p2p.enabled')) { $page->doXmlError('P2P disabled on this peer.'); }
	if (false == array_key_exists('message', $_POST)) { $page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $page->doXmlError('Peer UID not sent.'); }

	$model = new P2P_Peer($_POST['peer']);
	if (false == $model->loaded) { $page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $model->checkMessage($message, $signature)) {
		$page->doXmlError('Bad signature.');
	}

	//----------------------------------------------------------------------------------------------
	//	parse into array and store in database
	//----------------------------------------------------------------------------------------------
	$xd = new KXmlDocument($message);
	$children = $xd->getChildren();
	$objects = array();
	$reponse = '';

	foreach($children as $childId) {
		$add = true;
		$objXml = $xd->getInnerXml($childId, true);
		$objAry = $db->objectXmlToArray($objXml);
		$state = '';

		if (0 == count($objAry)) { $add = false; echo "<!-- COULD NOT PARSE XML -->\n"; }

		if (true == $db->objectExists($objAry['model'], $objAry['fields']['UID'])) {
			//--------------------------------------------------------------------------------------
			//	check that this object is newer than our own, if we have it
			//--------------------------------------------------------------------------------------
			$local = $db->getObject($objAry['model'], $objAry['fields']['UID']);
			$localTime = $kapenta->strtotime($local['editedOn']);
			$newTime = $kapenta->strtotime($objAry['fields']['editedOn']);

			if ($localTime > $newTime) {
				$add = false; 
				$response .= "<!-- Ours is more recent. -->\n";
			}
		}

		//--------------------------------------------------------------------------------------
		//	try save it if we want it
		//--------------------------------------------------------------------------------------
		if (true == $add) {
			$check = $db->storeObjectXml($objXml, false, false, false);
			if (true == $check) {
				$state = 'has'; }							// we now have it
			else {
				//$state = 'want';							// we still want it
				$state = 'dnw';								// but fail this for now
				$reponse .= "<!-- could not store object XML -->\n";
			}						
		} else { $state = 'dnw'; }							// we didn't want it

		//--------------------------------------------------------------------------------------
		//	let other peers know about it
		//--------------------------------------------------------------------------------------
		$args = array(
			'type' => 'object',
			'model' => $objAry['model'],
			'UID' => $objAry['fields']['UID'],
			'properties' => $objAry['fields'],
			'fileName' => '',
			'peer' => $model->UID
		);

		$kapenta->raiseEvent('*', 'object_received', $args);

		//--------------------------------------------------------------------------------------
		//	note this in the reply
		//--------------------------------------------------------------------------------------
		$response .= ''
		 . "\t<object>\n"
		 . "\t\t<model>" . $objAry['model'] . "</model>\n"
		 . "\t\t<UID>" . $objAry['fields']['UID'] . "</UID>\n"
		 . "\t\t<status>$state</status>\n"
		 . "\t</object>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<thankyou>\n" . $response . "</thankyou>\n";

?>
