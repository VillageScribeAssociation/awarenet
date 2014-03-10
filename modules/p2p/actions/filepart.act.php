<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	allow clients to retrieve a part of a file
//--------------------------------------------------------------------------------------------------
//+	Example message:
//+
//+		<part>
//+			<path>data/videos/1/2/3/12345678.flv</path>
//+			<hash>[sha1 hash of part]</hash>
//+			<size>512</size>
//+			<index>3</index>
//+		</part>
//+
//+	Note that part size is measured in kilobytes, and part number starts from 0.
//+
//postarg: message - XML document describing file part to be retrieved [string]
//postarg: signature - RSA 4096 signature of message [string]
//postarg: peer - UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and message signature
	//----------------------------------------------------------------------------------------------
	if ('yes' != $kapenta->registry->get('p2p.enabled')) { $kapenta->page->doXmlError('P2P disabled on this peer.'); }
	if (false == array_key_exists('message', $_POST)) { $kapenta->page->doXmlError('No message sent.'); }
	if (false == array_key_exists('signature', $_POST)) { $kapenta->page->doXmlError('No signature sent.'); }
	if (false == array_key_exists('peer', $_POST)) { $kapenta->page->doXmlError('Peer UID not sent.'); }

	$model = new P2P_Peer($_POST['peer']);
	if (false == $model->loaded) { $kapenta->page->doXmlError('Peer not recognized.'); }

	$message = base64_decode($_POST['message']);
	$signature = base64_decode($_POST['signature']);

	if (false == $model->checkMessage($message, $signature)) { $kapenta->page->doXmlError('Bad signature.'); }

	//----------------------------------------------------------------------------------------------
	//	parse the message
	//----------------------------------------------------------------------------------------------
	$xd = new KXmlDocument($message);
	$detail = $xd->getChildren2d();			//%	root node's children [dict]

	if (false == array_key_exists('path', $detail)) { $kapenta->page->doXmlError('Missing: path'); }
	if (false == array_key_exists('hash', $detail)) { $kapenta->page->doXmlError('Missing: hash'); }
	if (false == array_key_exists('size', $detail)) { $kapenta->page->doXmlError('Missing: size'); }
	if (false == array_key_exists('index', $detail)) { $kapenta->page->doXmlError('Missing: index'); }

	//----------------------------------------------------------------------------------------------
	//	get and return the file part
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->fs->exists($detail['path'])) { $kapenta->page->doXmlError('File not found.'); }

	$meta = new KLargeFile($detail['path']);
	$raw = $meta->getPart($detail['index']);
	if ('' == $raw) { $kapenta->page->doXmlError('Part could not be loaded: ' . $detail['index'] . '.'); }
	if (sha1($raw) != $detail['hash']) { $kapenta->page->doXmlError('Hash mismatch.'); }

	//TODO: discover if we really need to b64 encode, it'd be nice to remove this overhead

	echo base64_encode($raw);

?>
