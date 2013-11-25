<?php

//--------------------------------------------------------------------------------------------------
//	fired when an object is received
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of a p2p_peer object [string]
//arg: model - type of object which has been updated [string]
//arg: UID - UID of object which has been updated [string]
//arg: data - array of fields and values [dict]

function messages__cb_p2p_objectupdated($args) {
	global $kapenta;
	
	if (false == $kapenta->mcEnabled) { return false; }		//	nothing to do

	//----------------------------------------------------------------------------------------------
	//	check arguments and relevance to this module
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }
	if (false == is_array($args['data'])) { return false; }
	if (false == array_key_exists('owner', $args['data'])) { return false; }
	if ('messages_message' != $args['model']) { return false; }

	//----------------------------------------------------------------------------------------------
	//	invalidate cached message count for this mailbox
	//----------------------------------------------------------------------------------------------
	$kapenta->cacheDelete('pmcount::' . $args['data']['owner']);

	return true;
}

?>
