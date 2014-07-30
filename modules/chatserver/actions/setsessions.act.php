<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');

//--------------------------------------------------------------------------------------------------
//	action by which a client asserts / resets it local sessions list
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check client signature and arguments
	//----------------------------------------------------------------------------------------------
	//TODO: check signature here

	if (false == array_key_exists('me', $_POST)) { $page->doXmlError('peer not identified'); }
	if (false == array_key_exists('msg', $_POST)) { $page->doXmlError('msg not identified'); }

	$peerUID = $_POST['me'];				
	$msg = base64_decode($_POST['msg']);

	$peer = new Chatserver_Peer($peerUID, true);
	if (false == $peer->loaded) { $page->doXmlError("Unknown peer."); }

	//----------------------------------------------------------------------------------------------
	//	update the sessions set and respond to client
	//----------------------------------------------------------------------------------------------
	//header("Content-type: application/xml");

	$sessions = new Chatserver_Sessions($peer->peerUID, true);
	$response = $sessions->setSessions($msg);

	$response = "<sessions>\n" . $response . "</sessions>\n";

	echo $response;

?>
