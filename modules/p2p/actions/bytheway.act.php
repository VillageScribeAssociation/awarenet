<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//*	interface for peers to respond to offers
//--------------------------------------------------------------------------------------------------
//+	peers may not want a file of object for whatever reason, may have downloaded it successfully
//+	or may want to report some issues with an object.
//+
//+	Message should be an xml document describing it's reaction to an offer, reactions may be:
//+
//+		has - peer already has an up-to-date copy of this object 
//+		dnw - peer does not want this item
//+
//+	Example:
//+	
//+		<offers>
//+			<offer>
//+				<type>object</type>
//+				<refModel>moblog_post</refModel>
//+				<refUID>1234567890</refUID>
//+				<hash>[sha1]</hash>
//+				<response>has</response>
//+			</offer>
//+			<offer>
//+				<type>file</type>
//+				<filename>data/images/1/2/3/123456.jpg</filename>
//+				<hash>sha1</hash>
//+				<refUID>dnw</refUID>
//+			</offer>
//+		</offers>

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

	if (false == $model->checkMessage($message, $signature)) { $kapenta->page->doXmlError('Bad signtaure.'); }

	//----------------------------------------------------------------------------------------------
	//	record peer's response to the offers
	//----------------------------------------------------------------------------------------------
	$set = new P2P_Offers($model->UID, $message);
	$report .= $set->noteResponses();

	echo "<ok/>";
	//echo $report;

?>
