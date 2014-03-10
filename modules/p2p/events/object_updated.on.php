<?

	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of object which has changed [string]
//arg: UID - UID object which has been changed [string]

function p2p__cb_object_updated($args) {
	global $kapenta;
	global $kapenta; 
	global $user;
	global $kapenta;
	global $session;
	global $revisions;

	if (
		(true == array_key_exists('broadcast', $args)) &&
		('no' == $args['broadcast'])
	) {
		return false;													//	not to be passed on
	}

	if (false == array_key_exists('module', $args)) { return false; }	//	no module
	if (false == array_key_exists('model', $args)) { return false; }	//	no model
	if (false == array_key_exists('UID', $args)) { return false; }		//	no UID
	if (false == array_key_exists('data', $args)) { return false; }		//	missing fields

	$data = $args['data'];
	if (false == is_array($data)) { return false; }						//	invalid

	if (false == array_key_exists('editedOn', $data)) { return false; }	//	editedOn is mandatory

	if (true == array_key_exists('shared', $data)) {
		// we don't have any action to take if this object is not shared on the network
		if ('yes' != $args['data']['shared']) { return false; }
	}

	if (true == $revisions->isDeleted($args['model'], $args['UID'])) { return false; }
	if (false == $kapenta->db->isShared($args['model'], $args['UID'])) { return false; }

	//----------------------------------------------------------------------------------------------
	//	if an object has been updated assume all peers want to know about it
	//---------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('p2p_peer', '*', '');
	//$set = new P2P_Offers();						//	<-- remove this when possible

	foreach($range as $item) {
		//$msg = 'Sharing changes: ' . $args['model'] .'::'. $args['UID'] .' with '. $item['name'];
		//$session->msgAdmin($msg);

		//------------------------------------------------------------------------------------------
		//	DEPRECATED
		//------------------------------------------------------------------------------------------
		//$set->peerUID = $item['UID'];
		//$set->updateObject($args['model'], $args['UID'], $args['data']);

		//------------------------------------------------------------------------------------------
		//	record this update in output puffer file
		//------------------------------------------------------------------------------------------
		//TODO: store object priorities in the registry

		$updates = new P2P_Updates($item['UID']);

		$msg = $updates->encodeDbObject($args['model'], $data);
		$priority = $updates->getPriority($args['model']);

		$updates->storeMessage($msg, $priority);
		
	}

	return true;
}

//--------------------------------------------------------------------------------------------------

?>
