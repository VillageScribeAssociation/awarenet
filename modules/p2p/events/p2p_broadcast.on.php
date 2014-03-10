<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	event raised by this or other modules to broadcast some message to all peers
//--------------------------------------------------------------------------------------------------
//arg: message - XML document [string]
//arg: exclude - UID fo a P2P_Peer, we don't bounce messages back [string]
//arg: priority - 0 to 9, default is 9 [string]

function p2p__cb_p2p_broadcast($args) {
	global $kapenta;	

	$priority = 9;
	$exclude = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('message', $args)) { return false; }
	if (true == array_key_exists('priority', $args)) { $priority = (int)$args['priority']; }
	if (true == array_key_exists('exclude', $args)) { $exclude = $args['exclude']; }

	//----------------------------------------------------------------------------------------------
	//	load list of peers from database
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('p2p_peer', '*', '');

	foreach($range as $item) {
		if ($exclude != $item['UID']) {
			$updates = new P2P_Updates($item['UID']);
			$updates->storeMessage($args['message'], $priority);
		}
	}

	return true;
}

?>
