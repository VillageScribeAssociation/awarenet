<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//*	recieve a set of offers from a peer
//--------------------------------------------------------------------------------------------------
//+	Responds with the same list marked up to show the items we want.  Message format is the same as
//+	P2P_Offers::toXml()
//+
//postarg: message - offer type and timestamp requested [string]
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

	if (false == $model->checkMessage($message, $signature)) { $page->doXmlError('Bad signaure.'); }

	//----------------------------------------------------------------------------------------------
	//	parse the list and decide which of these items interest us
	//----------------------------------------------------------------------------------------------
	$set = new P2P_Offers($model->UID, $message);
	$set->evaluate();

	//----------------------------------------------------------------------------------------------
	//	return the list
	//----------------------------------------------------------------------------------------------
	echo $set->toXml();


?>
