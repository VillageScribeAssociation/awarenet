<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	a peer is giving us a file manifest
//--------------------------------------------------------------------------------------------------
//postarg: message - base64_encoded XML document (manifest) [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('message', $_POST)) { $kapenta->page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $kapenta->page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $kapenta->page->doXmlError('Peer UID not sent.'); }

	$peer = new P2P_Peer($_POST['peer']);
	if (false == $peer->loaded) { $kapenta->page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $peer->checkMessage($message, $signature)) { $kapenta->page->doXmlError('Bad signature.'); }

	//----------------------------------------------------------------------------------------------
	//	check and store the manifest
	//----------------------------------------------------------------------------------------------
	$klf = new KLargeFile();
	$klf->loadMetaXml($message);
	if (false == $klf->loaded) { $kapenta->page->doXmlError('Could not load manifest.'); }

	$check = $klf->saveMetaXml();	
	if (false == $check) { $kapenta->page->doXmlError('Could not save manifest.'); }

	echo "<ok/>";

?>
