<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	record a new peer instance which will chat through this point
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('chatserver', 'chatserver_peer', 'new')) {
		$page->do403('You are not authorized to create new Peers.');
	}

	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Peer();

	foreach($_POST as $key => $value) {
		switch(strtolower($key)) {
			case 'peeruid':		$model->peerUID = $value;		break;
			case 'name':		$model->name = $value;			break;
			case 'url':			$model->url = $value;			break;
			case 'pubkey':		$model->pubkey = $value;		break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new Peer<br/>', 'ok');
		$page->do302('/chatserver/');
	} else {
		$session->msg('Could not create new Peer:<br/>' . $report);
		$page->do302('/chatserver/');
	}

?>
