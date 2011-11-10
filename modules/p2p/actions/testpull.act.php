<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	development / debugging action which displays offers made by a given peer
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	$model = new P2P_Peer($req->ref);
	if (false == $model->loaded) { $page->do404('No such model.'); }

	$client = new P2P_Client($model->UID);

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h1>Testing pull from: " . $model->name . "</h1>\n";

	$report = $client->pull();
	echo $report;

	/*
	//----------------------------------------------------------------------------------------------
	//	send the message (server returns <ok/> if trusted
	//----------------------------------------------------------------------------------------------
	$xml = $model->sendMessage('offers', 'both|' . $kapenta->time());

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$xml</textarea>";

	echo "<h2>Offers</h2>";

	$set = new P2P_Offers($model->UID, $xml);

	echo "<h3>Before evaluation</h3>\n";
	echo $set->toHtml();

	$set->evaluate();

	echo "<h3>After evaluation</h3>\n";
	echo $set->toHtml();

	//---- cut

	//----------------------------------------------------------------------------------------------
	//	request any objects we want
	//----------------------------------------------------------------------------------------------
	echo "<h2>Downloading objects we want</h2>\n";

	$message = "<requests>\n";
	foreach($set->members as $item) {
		if ('want' == $item['status']) { 
			$message .= "\t<request>\n";
			$message .= "\t\t<model>" . $item['refModel'] . "</model>\n";
			$message .= "\t\t<UID>" . $item['refUID'] . "</UID>\n";
			$message .= "\t\t<updated>" . $item['updated'] . "</updated>\n";
			$message .= "\t\t<hash>" . $item['hash'] . "</hash>\n";
			$message .= "\t</request>\n";
		}
	}
	$message .= "</requests>";

	echo "<h3>sending: p2p/object/</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$message</textarea>";

	$response = $model->sendMessage('object', $message);
	echo "<h3>response:</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$response</textarea>";

	//----------------------------------------------------------------------------------------------
	//	save them to the database
	//----------------------------------------------------------------------------------------------

	$xdo = new KXmlDocument($response);
	$children = $xdo->getChildren();
	foreach ($children as $childId) {
		$kobjXml = $xdo->getInnerXml($childId, true);
		echo "<h3>object:</h3>";
		echo "<textarea rows='10' cols='80' style='width: 100%;'>$kobjXml</textarea>";

		$check = $db->storeObjectXml($kobjXml);
		if (true == $check) { 
			echo "<b>OBJECT STORED</b><br/>"; 

		} else {	
			echo "<B>object not stroed, database error</b><br/>";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	update peer about this
	//----------------------------------------------------------------------------------------------
	echo "<h2>Telling peer about it</h2>\n";
	echo "<h3>sending: p2p/bytheway/</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>" . $set->toXml() . "</textarea>";
	$btwresponse = $model->sendMessage('bytheway', $set->toXml());
	echo "<h3>response:</h3>";
	echo "<textarea rows='10' cols='80' style='width: 100%;'>$btwresponse</textarea>";
	*/

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');
?>
