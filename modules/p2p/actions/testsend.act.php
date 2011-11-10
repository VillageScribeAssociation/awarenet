<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	send a test message to another peer to check if they trust us
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$model = new P2P_Peer($req->ref);
	if (false == $model->loaded) { $page->do404('Unkown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	send the message (server returns <ok/> if trusted
	//----------------------------------------------------------------------------------------------
	$report = $model->sendMessage('testrecieve', 'this is a test message');

	if ('' == $report) {
		$session->msg('Test mesage sent, peer trusts us.', 'ok');
		$model->status = 'trusted';
		$model->save();

	} else {
		$session->msg('Test send failed:<br/>' . $report, 'bad');
		$model->status = 'unknown';
		$model->save();
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to peers console either way
	//----------------------------------------------------------------------------------------------
	$page->do302('p2p/peers/');

?>
