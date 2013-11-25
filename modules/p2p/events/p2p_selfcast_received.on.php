<?php

//--------------------------------------------------------------------------------------------------
//|	processes a selfcast (asynchronous) database update
//--------------------------------------------------------------------------------------------------
//arg: model - type of object being inserted/updated [string]
//arg: fields64 - base64 serialization of fields array, as defined by db wrapper [string]

function p2p__cb_p2p_selfcast_received($args) {
	global $db;
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('fields64', $args)) { return false; }
	if (false == $db->tableExists($args['model'])) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add to database
	//----------------------------------------------------------------------------------------------
	$data = $db->unserialize($args['fields64']);
	$dbSchema = $db->getSchema($args['model']);
	$db->save($data, $dbSchema, false, true, true);
}

?>
