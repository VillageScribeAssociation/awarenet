<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//*	returns the set of files this peer is currently downloading
//--------------------------------------------------------------------------------------------------
//+	This returns a set of downloads this peer is currently engaged in, like so:
//+	
//+		<downloads>
//+			<download>
//+				<fileName>data/images/1/2/3/somefile.jpg</fileName>
//+				<parts></parts>
//+			</download>
//+			<download>
//+				<fileName>data/videos/3/4/5/another.mp4</fileName>
//+				<parts>1110000010010</parts>
//+			</download>
//+		</downloads>
//+
//+	If the parts field is empty then this peer does not yet have a manifest for the download, 
//+	otherwise the zeros and ones represent parts we do and do not have.

//postarg: message - timestamp, to prevent replay of requests [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if ('yes' != $registry->get('p2p.enabled')) { $page->doXmlError('P2P disabled on this peer.'); }
	if (false == array_key_exists('message', $_POST)) { $page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $page->doXmlError('Peer UID not sent.'); }

	$peer = new P2P_Peer($_POST['peer']);
	if (false == $peer->loaded) { $page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $peer->checkMessage($message, $signature)) { $page->doXmlError('Bad signaure.'); }

	$timestamp = $message;
	//TODO: test timestamp

	//----------------------------------------------------------------------------------------------
	//	make the list of files this peer wants or is downloading
	//----------------------------------------------------------------------------------------------
	$downloads = new P2P_Downloads($peer->UID);
	$xml = $downloads->toXml();
	echo $xml;

?>
