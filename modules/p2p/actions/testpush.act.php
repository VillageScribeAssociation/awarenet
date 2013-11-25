<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	test pushing data to a peer server from behind a firewall
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	$model = new P2P_Peer($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('No such model.'); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
	echo "<h1>Testing push to: " . $model->name . "</h1>\n"; flush();

	$client = new P2P_Client($model->UID);

	$report = $client->push();
	echo $report;

	//----------------------------------------------------------------------------------------------
	//	fin
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');
?>
