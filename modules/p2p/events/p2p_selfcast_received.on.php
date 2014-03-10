<?php

//--------------------------------------------------------------------------------------------------
//|	processes a selfcast (asynchronous) database update
//--------------------------------------------------------------------------------------------------
//arg: model - type of object being inserted/updated [string]
//arg: fields64 - base64 serialization of fields array, as defined by db wrapper [string]

function p2p__cb_p2p_selfcast_received($args) {
	global $kapenta;
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('fields64', $args)) { return false; }
	if (false == $kapenta->db->tableExists($args['model'])) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add to database
	//----------------------------------------------------------------------------------------------
	$data = $kapenta->db->unserialize($args['fields64']);
	$dbSchema = $kapenta->db->getSchema($args['model']);
	$kapenta->db->save($data, $dbSchema, false, true, true);
}

?>
