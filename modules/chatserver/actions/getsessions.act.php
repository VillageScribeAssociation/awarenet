<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');	
	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');

//--------------------------------------------------------------------------------------------------
//*	returns an XML document listing active sessions on some peer to an authenticated client
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chatserver_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and client signature
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->doXmlError('Reference not given.'); }
	//TODO: check signature here

	$peer = new Chatserver_Peer($kapenta->request->ref, true);
	if (false == $peer->loaded) { $page->doXmlError('Unknown peer: ' . $kapenta->request->ref); }

	//----------------------------------------------------------------------------------------------
	//	make the list
	//----------------------------------------------------------------------------------------------

	$set = new Chatserver_Sessions($peer->peerUID, true);
	echo $set->toXml();

?>
