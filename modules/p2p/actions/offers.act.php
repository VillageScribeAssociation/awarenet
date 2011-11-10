<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//*	returns a list of offers for another peer
//--------------------------------------------------------------------------------------------------
//+
//+	Message should take the form [offertype]|[timestamp] where offer type is one of
//+
//+		objects - client only wants objects at this time
//+		files - client only wants files at this time
//+		both - client will accept any objects atthis time
//+
//+	Timestamp should be within a few minutes, to reduce message replay
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

	$parts = explode('|', $message);
	$type = $parts[0];
	$timestamp = $parts[1];
	//TODO: test timestamp

	//----------------------------------------------------------------------------------------------
	//	send the offers
	//----------------------------------------------------------------------------------------------
	$set = new P2P_Offers($model->UID);
	$set->load($type);
	echo $set->toXml();

?>
