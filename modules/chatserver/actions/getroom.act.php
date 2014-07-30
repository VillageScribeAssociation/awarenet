<?

	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	get details of a chat room given its UID
//--------------------------------------------------------------------------------------------------
//req: UID of a Chatsever_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and authenticate client
	//----------------------------------------------------------------------------------------------
	// TODO: identify client and check RSA signature

	if ('' == $kapenta->request->ref) { $page->doXmlError('Room UID not given'); }

	$model = new Chatserver_Room($kapenta->request->ref);
	if (false == $model->loaded) { $page->doXmlError('Unknown room.'); }

	//----------------------------------------------------------------------------------------------
	//	send room data
	//----------------------------------------------------------------------------------------------
	header("Content-type: application/xml");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
	echo $model->toXml();

?>
