<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	test pushing data to a peer server from behind a firewall
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	$model = new P2P_Peer($req->ref);
	if (false == $model->loaded) { $page->do404('No such model.'); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h1>Testing push to: " . $model->name . "</h1>\n"; flush();

	$client = new P2P_Client($model->UID);

	$report = $client->push();
	echo $report;

	/*

	//----------------------------------------------------------------------------------------------
	//	make a list of offers and send it to the /giveoffers/ interface
	//----------------------------------------------------------------------------------------------
	$set = new P2P_Offers($model->UID);
	$set->load('objects');
	$message = $set->toXml();
	echo "<h3>sending: p2p/giveoffers/</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$message</textarea>";
	//echo $set->toHtml();
	$response = $model->sendMessage('giveoffers', $message);

	echo "<h3>response: p2p/giveoffers/</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

	//----------------------------------------------------------------------------------------------
	//	load the set returned by /giveoffers/ and collect the items this peer indicates it wants
	//----------------------------------------------------------------------------------------------
	$newSet = new P2P_Offers($model->UID, $response);
	echo $newSet->toHtml();

	$objectsXml = '';

	echo "<h3>Checking response...</h3>\n";
	foreach($newSet->members as $idx => $item) {
		if ('want' == $item['status']) {
			if ('object' == $item['type']) {
				echo "Peer would like us to send: ". $item['refModel'] .'::'. $item['refUID'] ."<br/>";
				$objectsXml .= $db->getObjectXml($item['refModel'], $item['refUID']);
				echo "ADDING: " . $db->getObjectXml($item['refModel'], $item['refUID']) . "<br/>";
			}

			//TODO: collect files here
		}
	}

	//----------------------------------------------------------------------------------------------
	//	send all objects the peer wants
	//----------------------------------------------------------------------------------------------

	if ('' != $objectsXml) {
		
		$objectsXml = "<kobjects>\n" . $objectsXml . "</kobjects>\n";		

		echo "<h3>sending objects: p2p/giveobject/</h3>";
		echo "<textarea rows='10' cols='80' style='width: 100%;'>$objectsXml</textarea>";

		$response = $model->sendMessage('giveobject', $objectsXml);

		echo "<h3>peer responds:</h3>";
		echo "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

		//------------------------------------------------------------------------------------------
		//	interpret this response
		//------------------------------------------------------------------------------------------
		$xdty = new KXmlDocument($response);
		$children = $xdty->getChildren();
		foreach($children as $childId) {
			$ty = $xdty->getChildren2d($childId);
			echo "Response to: " . $ty['model'] . '::' . $ty['UID'] . ' was ' . $ty['status'] . "<br/>";

			foreach($newSet->members as $idx => $item) {
				if (($item['refModel'] == $ty['model']) && ($item['refUID'] == $ty['UID'])) {
					echo "Updated set index $idx<br/>\n";
					$newSet->members[$idx]['status'] = $ty['status'];
				}
			}

		}

	} else {
		echo "<h3>Peer does not want any of the offered objects.</h3>";
	}

	//----------------------------------------------------------------------------------------------
	//	update P2P_Gifts table based on responses
	//----------------------------------------------------------------------------------------------

	echo "<h3>Updating local gifts table.</h3>";	

	foreach($newSet->members as $idx => $item) {
		$model = new P2P_Gift($item['UID']);
		if ((true == $model->loaded) && ($model->status != $item['status'])) {
			$model->status = $item['status'];
			$model->save();
			echo "updated gift: " . $item['status'] . " (" . $item['refModel'] . '::' . $item['refUID'] . ")<br/>\n";
		} else {
			echo "could not load gift: " . $item['UID'] . "<br/>\n";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	send all files the peer wants
	//----------------------------------------------------------------------------------------------
	//TODO: this
	*/
	//----------------------------------------------------------------------------------------------
	//	fin
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
?>
