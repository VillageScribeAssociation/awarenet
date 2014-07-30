<?

	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	get set of memberships of given room
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chatserver_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and use signature
	//----------------------------------------------------------------------------------------------
	//TODO: check signature here

	if ('' == $kapenta->request->ref) { $page->doXmlError("Room not given."); }
	$model = new Chatserver_Room($kapenta->request->ref);
	if (false == $model->loaded) { $page->doXmlError("Unknown room."); }

	//----------------------------------------------------------------------------------------------
	//	return list of memberships
	//----------------------------------------------------------------------------------------------
	header("Content-type: application/xml");
	echo $model->memberships->toXml();

?>
