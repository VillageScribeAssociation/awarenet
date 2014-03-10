<?php

//--------------------------------------------------------------------------------------------------
//|	resynchronize a table with a peer, usually sent by self
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of a P2P_Peer object [string]
//arg: table - table to syncronize [string]
//arg: start - UID position to start from (integer) [string]
//arg: num - number of items to resynchronize (integer) [string]

function p2p__cb_p2p_resyncbatch_received($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peer', $args)) { return false; }
	if (false == array_key_exists('table', $args)) { return false; }
	if (false == array_key_exists('start', $args)) { return false; }
	if (false == array_key_exists('num', $args)) { return false; }
	if (false == $kapenta->db->tableExists($args['table'])) { return false; }
	if (false == $kapenta->db->objectExists('p2p_peer', $args['peer'])) { return false; }

	$table = $args['table'];
	$start = (int)$args['start'];
	$num = (int)$args['num'];

	$objects = '';
	$objectCount = 0;

	$dbSchema = $kapenta->db->getSchema($table);			//%	table schema [array]

	//----------------------------------------------------------------------------------------------
	//	load a batch of objects
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange($table, '*', '', 'UID ASC', $num, $start);

	foreach($range as $item) {

		$isShared = $kapenta->db->isShared($table, $item['UID']);			//%	share status [bool]
		$hasFile = array_key_exists('fileName', $item);	//% [bool]

		//if (true == $isShared) { $html .= "Database reports that object is shared.<br/>"; }
		//else { $html .= "Database reports that object is NOT SHARED.<br/>"; }

		if (true == $isShared) {
			//--------------------------------------------------------------------------------------
			// item is shared, (re)add for this peer
			//--------------------------------------------------------------------------------------
			$objects .= '      ' . $item['UID'] . '|' . $item['editedOn'] . "\n";
			$objectCount++;
			
			if ($objectCount >= 50) {		//	TODO: make this a registry setting
				p2p_share_throw($table, $objects);
				$objects = '';
				$objectCount = 0;
			}

			if ((true == $hasFile) && (true == array_key_exists('hash', $item))) {
				p2p_file_throw(
					$item['fileName'], $item['hash'],
					$dbSchema['module'], $table, $item['UID']
				);
				//$html .= "Object may have an attached file.<br/>";
			}

		} else {
			//------------------------------------------------------------------------------
			// item is not shared, do nothing
			//------------------------------------------------------------------------------
		}
	}

	//	handle any outstanding items
	if ($objectCount > 0) { p2p_share_throw($table, $objects); }

	return true;

}

//--------------------------------------------------------------------------------------------------
//|	utility function to add contents of the buffer to peer message queues as announcment
//--------------------------------------------------------------------------------------------------

function p2p_share_throw($tableName, $buffer) {
	global $kapenta;

	$msg = ''
	 . "  <announcement>\n"
	 . "    <model>" . $tableName . "</model>\n"
	 . "    <list>\n"
	 . $buffer
	 . "    </list>\n"
	 . "  </announcement>\n";

	$detail = array(
		'priority' => '6',
		'message' => $msg,
		'exclude' => ''
	);

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);	
}

//--------------------------------------------------------------------------------------------------
//|	utility function to announce a file to peers
//--------------------------------------------------------------------------------------------------
//arg: fileName - canonical location of file [string]
//arg: hash - sha1 hash of file [string]
//arg: refModule - module this file is part of [string]
//arg: refModel - type of object which owns file [string]
//arg: refUID - UID of object which owns file [string]

function p2p_file_throw($fileName, $hash, $refModule, $refModel, $refUID) {
	global $kapenta;
	global $kapenta;

	$msg = ''
	 . "<fileoffer>\n"
	 . "  <module>" . $refModule . "</module>\n"
	 . "  <model>" . $refModel . "</model>\n"
	 . "  <uid>" . $refUID . "</uid>\n"
	 . "  <filename>" . $fileName . "</filename>\n"
	 . "  <hash>" . $hash . "</hash>\n"
	 . "  <peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
	 . "</fileoffer>\n";

	$detail = array(
		'message' => $msg,
		'priority' => '5',
		'exclude' => ''
	);

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);
}

?>
