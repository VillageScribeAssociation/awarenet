<?php

//--------------------------------------------------------------------------------------------------
//*	peer requests we resynchronize all content
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of a P2P_Peer object [string]

function p2p__cb_p2p_resynchronize_received($args) {
	global $kapenta;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peer', $args)) { return false; }

	$tables = $db->listTables();			//	array of table names [array:string]
	$peerUID = $args['peer'];				//	UID of a P2P_Peer object [string]
	$priority = '2';						//	queue priority [int]

	//TODO: make batch size a registry key [string]

	//----------------------------------------------------------------------------------------------
	//	selfcast tables individually
	//----------------------------------------------------------------------------------------------
	$exclude = array(
		'revisions_revision',
		'p2p_peer',
		'p2p_gift',
		'wiki_mwimport'
	);

	foreach($tables as $table) {
		if ('tmp_' != substr($table, 0, 4)) { 

			$xml = ''
			 . "  <resynctable>\n"
			 . "      <peer>" . $args['peer'] . "</peer>\n"
			 . "      <table>" . $table . "</table>\n"
			 . "  </resynctable>\n";

			list($usec, $sec) = explode(" ", microtime());
			$fileName = ''
			 . 'data/p2p/received/'
			 . $priority . '_' . $peerUID . '_' . $sec . '_' . $usec . '.evt';

			if (false == in_array($table, $exclude)) { $kapenta->fs->put($fileName, $xml); }

		} // end if not temp table

	} // end foreach table

	return true;
}

?>
