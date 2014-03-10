<?php

//--------------------------------------------------------------------------------------------------
//|	peer has requested we resynchronize a table
//--------------------------------------------------------------------------------------------------
//;	since tables may have a very large number of rows, this operation is broken into batches,
//;	each with their own async event
//arg: peer - UID of a P2P_Peer object [string]
//arg: table - name of table to resync/reannounce [string]

function p2p__cb_p2p_resynctable_received($args) {
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peer', $args)) { return false; }
	if (false == array_key_exists('table', $args)) { return false; }
	if (false == $kapenta->db->tableExists($args['table'])) { return false; }
	if (false == $kapenta->db->objectExists('p2p_peer', $args['peer'])) { return false; }

	$priority = '2';						//	queue priority [int]
	$batchsize = 50;						//	number of items per child event [int]
	$start = 0;								//	counter [int]

	//----------------------------------------------------------------------------------------------
	//	break table into batches of objects
	//----------------------------------------------------------------------------------------------
	$total = $kapenta->db->countRange($args['table']);

	for ($start = 0; $start < $total; $start += $batchsize) {

			$xml = ''
			 . "  <resyncbatch>\n"
			 . "      <peer>" . $args['peer'] . "</peer>\n"
			 . "      <table>" . $args['table'] . "</table>\n"
			 . "      <start>" . $start . "</start>\n"
			 . "      <num>" . $batchsize . "</num>\n"
			 . "  </resyncbatch>\n";

			list($usec, $sec) = explode(" ", microtime());
			$fileName = ''
			 . 'data/p2p/received/'
			 . $priority . '_' . $args['peer'] . '_' . $sec . '_' . $usec . '.evt';

			$kapenta->fs->put($fileName, $xml);

	}

	return true;
}

?>
