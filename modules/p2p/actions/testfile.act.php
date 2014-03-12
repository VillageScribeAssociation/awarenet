<?

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	development action to test request of large file metadata
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Peer UID not given'); }

	$fileName = 'data/videos/1/1/0/110908755616157252';

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->do404('Unkown peer.'); }

	$xml = $peer->sendMessage('file', $fileName);

	echo '<pre>' . htmlentities($xml) . '</pre>';

	$klf = new KLargeFile($fileName);
	echo "metaFile: " . $klf->metaFile . "<br/>";
	$kapenta->fs->put($klf->metaFile, $xml, true, true);
	$klf->loadMetaXml();
	
	echo "<h2>Check</h2>\n";

	echo '<pre>' . htmlentities($klf->toXml()) . '</pre>';
?>
