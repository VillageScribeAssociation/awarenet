<?php

//--------------------------------------------------------------------------------------------------
//*	called when an object is saved to the database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function users__cb_object_updated($args) {
	global $cache;
	global $db;

	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	$data = $args['data'];

	//----------------------------------------------------------------------------------------------
	//	clear cached summaries when a users_user object is saved
	//----------------------------------------------------------------------------------------------

	if ('users_user' == $args['model']) {
		$cache->clear('users-summarynav-' . $args['UID']);
		//	^	add more channels here
	}

	return true;
}

?>
