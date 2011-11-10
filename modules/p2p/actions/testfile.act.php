<?

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	development action to test request of large file metadata
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Peer UID not given'); }

	$fileName = 'data/videos/1/1/0/110908755616157252';

	$peer = new P2P_Peer($req->ref);
	if (false == $peer->loaded) { $page->do404('Unkown peer.'); }

	$xml = $peer->sendMessage('file', $fileName);

	echo '<pre>' . htmlentities($xml) . '</pre>';

	$klf = new KLargeFile($fileName);
	echo "metaFile: " . $klf->metaFile . "<br/>";
	$kapenta->filePutContents($klf->metaFile, $xml, true, true);
	$klf->loadMetaXml();
	
	echo "<h2>Check</h2>\n";

	echo '<pre>' . htmlentities($klf->toXml()) . '</pre>';
?>
