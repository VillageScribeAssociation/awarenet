<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	peer is giving us part of a file we requested
//--------------------------------------------------------------------------------------------------
//+	Example message:
//+
//+		<part>
//+			<path>data/videos/1/2/3/123456789.flv</path>
//+			<index>2</index>
//+			<size>524288</size>
//+			<hash>[sha1 hash of raw part]</hash>
//+			<content64>[base64 encoded file part]</content64>
//+		</part>
//+
//postarg: message - base [string]
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

	if (false == $peer->checkMessage($message, $signature)) { $page->doXmlError('Bad signature.'); }

	//----------------------------------------------------------------------------------------------
	//	parse into array
	//----------------------------------------------------------------------------------------------
	$xd = new KXmlDocument($message);
	$detail = $xd->getChildren2d();

	if (false == array_key_exists('path', $detail)) { $page->doXmlError('Path not given'); }
	if (false == array_key_exists('index', $detail)) { $page->doXmlError('index not given'); }
	if (false == array_key_exists('size', $detail)) { $page->doXmlError('size not given'); }
	if (false == array_key_exists('hash', $detail)) { $page->doXmlError('hash not given'); }
	if (false == array_key_exists('content64', $detail)) { $page->doXmlError('content not given'); }

	$meta = new KLargeFile($detail['path']);
	if (false == $meta->loaded) { $page->doXmlError('Unknown manifest.'); }

	//----------------------------------------------------------------------------------------------
	//	check hash, if we still want this, etc
	//----------------------------------------------------------------------------------------------
	$allOk = true;
	$raw = base64_decode($detail['content64']);
	$hash = sha1($raw);
	if ($hash != $detail['hash']) { $page->doXmlError('Hash mismatch.'); }

	//----------------------------------------------------------------------------------------------
	//	save part to disk
	//----------------------------------------------------------------------------------------------
	$check = $meta->storePart($detail['index'], $detail['content64'], $detail['hash']);
	if (false == $check) { $page->doXmlError('Cound not store part.'); }
	$meta->saveMetaXml();

	//----------------------------------------------------------------------------------------------
	//	if complete then stitch together, delete manifest and remove from downloads list
	//----------------------------------------------------------------------------------------------
	if (true == $meta->checkCompletion()) {
		//	stitch together
		$check = $meta->stitchTogether();
		if (false == $check) { $page->doXmlError('Could not join file parts'); }
		
		//	raise an event on receipt of file (triggers sharing with other peers)

		$args = array(
			'module' => $meta->module,
			'model' => $meta->model,
			'UID' => $meta->UID,
			'fileName' => $meta->path,
			'peer' => $peer->UID
		);

		/*
		echo ''
		 . "raising file_received event:"
		 . " module:" . $args['module']
		 . " model:" . $args['model']
		 . " UID:" . $args['UID']
		 . " fileName: " . $args['fileName']
		 . " peer: " . $args['peer'] . "<br/>\n";
		*/

		$kapenta->raiseEvent('*', 'file_received', $args);		

		//	delete the manifest
		$check = $meta->delete();
		if (false == $check) { $page->doXmlError('Could not remove meta file.'); }

		//	remove file from download list
		$downloads = new P2P_Downloads($peer->UID);
		$check = $downloads->remove($detail['path']);
		if (false == $check) { $page->doXmlError('Could not remove completed download.'); }
		$check = $downloads->save();
		if (false == $check) { $page->doXmlError('Could not save download list.'); }
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<ok/>";
?>
