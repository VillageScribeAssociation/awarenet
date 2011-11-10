<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	allows clients to request file metadata
//--------------------------------------------------------------------------------------------------
//postarg: message - location of a file relative to installPath [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if ('yes' != $registry->get('p2p.enabled')) { $page->doXmlError('P2P disabled on this peer.'); }
	if (false == array_key_exists('message', $_POST)) { $page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $page->doXmlError('Peer UID not sent.'); }

	$model = new P2P_Peer($_POST['peer']);
	if (false == $model->loaded) { $page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $model->checkMessage($message, $signature)) { $page->doXmlError('Bad signature.'); }

	//----------------------------------------------------------------------------------------------
	//	generate the file metadata
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->fileExists($message)) { $page->doXmlError('File not found.'); }

	$meta = new KLargeFile($message);
	$meta->makeFromFile();
	$xml = $meta->toXml();
	echo $xml;

?>
