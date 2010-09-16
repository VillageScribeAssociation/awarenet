<?

//-------------------------------------------------------------------------------------------------
//	recieve a notice from peer (some event pages may need to action)
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/sync/models/deleted.mod.php');

	//---------------------------------------------------------------------------------------------
	//	authorize
	//---------------------------------------------------------------------------------------------
	if ($sync->authenticate() == false) { 
		$kapenta->logSync("could not authenticate notice reciept\n");
		$page->doXmlError('could not authenticate'); 
	}
	$kapenta->logSync("authenticated notice reciept\n");

	//---------------------------------------------------------------------------------------------
	//	details of notification
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('detail', $_POST) == false) { 	$page->doXmlError('deletion notice not sent'); }


	$channelID = '';
	$event = '';
	$data = '';

	$xe = new XmlEntity($_POST['detail']);
	foreach($xe->children as $child) {
		if ($child->type == 'channelid') { $channelID = $child->value; }
		if ($child->type == 'event') { $event = $child->value; }
		if ($child->type == 'data') { $data = $child->value; }
	}

	if ($channelID == '') { $page->doXmlError('channelID not specified'); }
	if ($event == '') { $page->doXmlError('event not specified'); }

	$syncHeaders = syncGetHeaders();
	$kapenta->logSync("recieved notice of $event on channel $channelID from " . $syncHeaders['Sync-Source'] . " \n");

	//---------------------------------------------------------------------------------------------
	//	notify clients
	//---------------------------------------------------------------------------------------------
	notifyChannel($channelID, $event, $data, false);
	$kapenta->logSync("channelID: $channelID event: $event data: $data \n");

	//---------------------------------------------------------------------------------------------
	//	pass on to peers
	//---------------------------------------------------------------------------------------------	
	$syncHeaders = syncGetHeaders();
	$source = $syncHeaders['Sync-Source'];
	$sync->broadcastNotification($source, $channelID, $event, $data);

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	

	echo "<ok/>";

?>
