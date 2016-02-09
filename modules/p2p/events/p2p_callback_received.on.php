<?php

//--------------------------------------------------------------------------------------------------
//|	processes an asynchronous event deferred to the background process
//--------------------------------------------------------------------------------------------------
//arg: target - module name or * [string]
//arg: event - name of event to raise [string]
//arg: arsg64 - base64 encoding of arguments [string]

function p2p__cb_p2p_callback_received($args) {
	global $kapenta;
	
	echo "fired: p2p_callback_received\n";
	print_r($args);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('target', $args)) { return false; }
	if (false == array_key_exists('event', $args)) { return false; }
	if (false == array_key_exists('args64', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add to database
	//----------------------------------------------------------------------------------------------
	$argsArray = $kapenta->db->unserialize($args['args64']);
	
	$kapenta->raiseEvent($args['target'], $args['event'], $argsArray);
}

?>
