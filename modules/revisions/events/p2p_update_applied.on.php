<?php

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a new/updated object is received from a peer
//-------------------------------------------------------------------------------------------------
//arg: peer - UIF of peer this update was received from [string]
//arg: model - type of object received [string]
//arg: fields - dict of key => value pairs [array]

function revisions__cb_p2p_update_applied($args) {
	global $kapenta;
	global $revisions;
	global $kapenta;
	global $kapenta;

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "revisions module caught p2p_update_applied<br/>\n";
		print_r($args);
	}

	//---------------------------------------------------------------------------------------------
	//	check arguments and relevance
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }		//	invalid
	if ('revisions_deleted' != $args['model']) { return false; }			//	not relevant
	if (false == array_key_exists('fields', $args)) { return false; }		//	invalid
	if (false == is_array($args['fields'])) { return false; }				//	invalid

	$model = $args['model'];
	$fields = $args['fields'];

	if (false == $kapenta->db->tableExists($model)) { return false; }				//	no such table
	if (false == array_key_exists('UID', $fields)) { return false; }		//	invalid
	if (false == array_key_exists('status', $fields)) { return false; }		//	invalid

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "revisions module caught p2p_update_applied - event passes tests<br/>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	remove deleted object from live dataset and all levels of cache
	//---------------------------------------------------------------------------------------------
	if (
		(('deleted' == $fields['status']) || ('delete' == $fields['status'])) &&
		(true == $kapenta->db->objectExists($fields['refModel'], $fields['refUID']))
	) {

		//	loaded first to reduce impact of potential SQL injection in UID field
		//	TODO: improve on this
		$objAry = $kapenta->db->getObject($fields['refModel'], $fields['refUID']);

		//	remove from memcache, TODO: move to cache_invalidation event on admin module
		$cacheKey = $fields['refModel'] . '::' . $fields['refUID'];
		$kapenta->cacheDelete($cacheKey);
		

		$sql = "DELETE FROM `" . $fields['refModel'] . "` where UID='" . $fields['refUID'] . "';";
		$kapenta->db->query($sql);
		//	TODO: check result and log any errors

		if ('yes' == $kapenta->registry->get('p2p.debug')) { echo "SQL: $sql \n"; }

		//-----------------------------------------------------------------------------------------
		//	invalidate caches
		//-----------------------------------------------------------------------------------------
		$detail = array(
			'model' => $fields['refmodel'],
			'UID' => $fields['refUID'],
			'data' => $objAry
		);

		$kapenta->raiseEvent('*', 'cache_invalidate', $detail);

		if ('yes' == $kapenta->registry->get('p2p.debug')) { echo "Invaliding cache: \n"; print_r($detail); }

	}

	//---------------------------------------------------------------------------------------------
	//	restore deleted object to live dataset
	//---------------------------------------------------------------------------------------------
	if (
		('restore' == $fields['status']) &&
		(false == $kapenta->db->objectExists($fields['refModel'], $fields['refUID']))
	) {
		$model = new Revisions_Deleted($fields['UID']);
		$check = $model->restore();

		if (false == $check) {
			//	TODO: log any errors
			return false;
		}
	}

	return true;
}

?>
