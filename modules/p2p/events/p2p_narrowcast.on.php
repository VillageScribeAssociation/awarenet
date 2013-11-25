<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	event raised by this or other modules to broadcast some message a single peer
//--------------------------------------------------------------------------------------------------
//arg: message - XML document [string]
//arg: peer - UID for a P2P_Peer [string]
//arg: priority - 0 to 9, default is 9 [string]

function p2p__cb_p2p_narrowcast($args) {
	global $db;	
	global $kapenta;

	$priority = 9;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('message', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { return false; }
	if (true == array_key_exists('priority', $args)) { $priority = (int)$args['priority']; }

	if (false == $db->objectExists('p2p_peer', $args['peer'])) { return false; }

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "Narrowcasting:";
		print_r($args);
	}

	//----------------------------------------------------------------------------------------------
	//	load list of peers from database
	//----------------------------------------------------------------------------------------------
	$updates = new P2P_Updates($args['peer']);
	$updates->storeMessage($args['message'], $priority);

	return true;
}

?>
