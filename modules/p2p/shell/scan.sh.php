<?php

	require_once('../../../shinit.php');
	require_once('../models/gift.mod.php');
	require_once('../models/peer.mod.php');
	require_once('../models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//	scan database for gifts
//--------------------------------------------------------------------------------------------------

	$usage_notes = ''
	 . "Usage: scan.sh.php [peerUID]|[peerName]|[peerUrl]\n"
	 . "This script will rescan the database for gift objects.\n\n";

	if (1 == count($argv)) { echo $argv[1] = ''; }

	$range = $db->loadRange('p2p_peer', '*');
	$peerUID = '';

	foreach($range as $item) {
		if ($item['UID'] == $argv[1]) { $peerUID = $item['UID']; }
 		if (strtolower($item['name']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		if (strtolower($item['url']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		$usage_notes .= "peer: " . $item['UID'] . ' - ' . $item['name'] . " - " . $item['url'] . "\n";
	}
	$usage_notes .= "\n\n";

	if ('' == $peerUID) { echo $usage_notes; die(); }

	$peer = new P2P_Peer($peerUID);
	if (false == $peer->loaded) { echo $usage_notes; die(); }

	$offers = new P2P_Offers($peer->UID);	

	/* ------------------------------------------------------------------------------------------ */

	$count = 0;												//%	return value
	$print = true;

	if ($print) { echo "Scanning database for: " . $peer->name . " (" . $peer->url . ")\n"; }

	//----------------------------------------------------------------------------------------------
	//	get list of tables
	//----------------------------------------------------------------------------------------------

	$tables = array();
	$allTables = $db->loadTables();
	foreach($allTables as $table)
	{
		if (('p2p_gift' != $table) && ('wiki_mwimport' != $table)) { $tables[] = $table; }
	}

	//----------------------------------------------------------------------------------------------
	//	check all items in this table
	//----------------------------------------------------------------------------------------------
	foreach($tables as $table) {
		$dbSchema = $db->getSchema($table);					//% db table definition [array]
		$sql = "select * from " . strtolower($table);		//%	everything in table [string]
		$so = '';											//%	filter to shared items [string]

		if (true == array_key_exists('shared', $dbSchema['fields'])) {
			$so = " where shared='yes'";
		}

		$result = $db->query($sql);							//%	recordset handle [int]

		if (true == $print) { echo "Searching table: $table\n"; }
		if (true == $print) { echo "Query: " . $sql . "<br/>\n"; }

		while($row = $db->fetchAssoc($result)) {
			$item = $db->rmArray($row);						//%	clean of db markup [dict]
			$add = true;									//%	not everything is added [bool]

			if (true == $print) {
				echo "$table::" . $item['UID'] . " "; 
			}

			if (true == $revisions->isDeleted($table, $item['UID'])) {
				echo "This item is deleted... ";
				$add = false;
			}
			if (false == $db->isShared($table, $item['UID'])) {
				echo "This item is not shared... ";
				$add = false;
			}

			if (true == $add) {
				//------------------------------------------------------------------------------
				//	check/update extant gift, make a new one if not found
				//------------------------------------------------------------------------------
				$check = $offers->updateObject($table, $item['UID'], $item);
				if (true == $check) { 
					$count++; 
					if (true == $print) { echo "Updating gift... "; }
				} else {
					if (true == $print) { echo "No change... "; }
				}

			} else {
				//------------------------------------------------------------------------------
				//	item should not be in gifts table, make sure of that it is not
				//------------------------------------------------------------------------------
				$giftUID = $offers->getGiftUID('object', $table, $item['UID']);
				if ('' != $giftUID) {
					$model = new P2P_Gift($giftUID);
					$model->delete();
					if (true == $print) { echo "Removed $giftUID from gifts table... "; }
				} else {
					if (true == $print) { echo "Not shared... "; }
				}
			}

			echo "\n";

		} // end while in recordset
		if (true == $print) { echo "\n"; }

	} // end for each table

	echo "\n\n$count gifts updated.\n\n";

?>
