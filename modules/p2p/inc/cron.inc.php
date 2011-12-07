<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]
//TODO: implement hours for which objects and files may be synced

function p2p_cron_tenmins() {
	global $db;
	global $kapenta;
	
	$report = "<h2>p2p_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	push or pull from all peers
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "status='trusted'";
	$range = $db->loadRange('p2p_peer', '*', '');

	foreach($range as $item) {
		if ('no' == $item['firewalled']) {
			$client = new P2P_Client($item['UID']);
			$detail = $client->push();
			$kapenta->logP2P($detail);
			$report .= "Pushing to: " . $item['name'] . " (" . $item['UID'] . ")<br/>\n";
			$report .= "Detail: " . strlen($detail) . " bytes<br/>\n";

			$detail = $client->pull();
			$kapenta->logP2P($detail);
			$report .= "Pulling from: " . $item['name'] . " (" . $item['UID'] . ")<br/>\n";
			$report .= "Detail: " . strlen($detail) . " bytes<br/>\n";

			$report .= $client->pullFiles();
			$report .= $client->pushFiles();


		} else {
			$report .= ''
			 . "Not connecting to firewalled peer: ". $item['name'] ." (". $item['UID'] .")<br/>\n";
		}
		$report .= "<br/>";
	}
	
	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	return $report;
}

?>
