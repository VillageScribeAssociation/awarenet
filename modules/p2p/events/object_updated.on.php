<?

	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function p2p__cb_object_updated($args) {
	global $kapenta;
	global $db; 
	global $user;
	global $page;
	global $session;
	global $revisions;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	if (false == array_key_exists('editedOn', $args['data'])) { return false; }

	if (true == array_key_exists('shared', $args['data'])) {
		// we don't have any action to take if this object is not shared on the network
		if ('yes' != $args['data']['shared']) { return false; }
	}

	if (true == $revisions->isDeleted($args['model'], $args['UID'])) { return false; }

	//----------------------------------------------------------------------------------------------
	//	if an object has been updated assume all peers want to know about it
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('p2p_peer', '*', '');
	$set = new P2P_Offers();

	foreach($range as $item) {
		//$msg = 'Sharing changes: ' . $args['model'] .'::'. $args['UID'] .' with '. $item['name'];
		//$session->msgAdmin($msg);

		$set->peerUID = $item['UID'];
		$set->updateObject($args['model'], $args['UID'], $args['data']);
	}

	return true;
}

//--------------------------------------------------------------------------------------------------

?>
