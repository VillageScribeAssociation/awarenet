<?

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/worker.class.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell script to repeatedly pull from a peer
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	$worker = new P2P_Worker(true);		//	true - make it chatty

	$continue = true;

	while (true == $continue) {
		$continue = $worker->dispatch();
	}

	echo "All queues empty.\n";

?>
