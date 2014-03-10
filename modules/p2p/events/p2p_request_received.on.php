<?php

//--------------------------------------------------------------------------------------------------
//*	fired when a peer requests a database object from us
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of peer this was received from [string]
//arg: model - type of object [string]
//arg: uid - UID of an object [string]

function p2p__cb_p2p_request_received($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check the update
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { $args['peer'] = ''; }
	if (false == array_key_exists('priority', $args)) { $args['priority'] = '5'; }

	if ('yes' == $kapenta->registry->get('p2p.debug')) { print_r($args); }

	$model = $args['model'];
	$UID = $args['uid'];
	
	//----------------------------------------------------------------------------------------------
	//	check we have this
	//----------------------------------------------------------------------------------------------
	
	if (false == $kapenta->db->tableExists($model)) { return false; }
	if (false == $kapenta->db->objectExists($model, $UID)) { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	narrowcast to this peer
	//----------------------------------------------------------------------------------------------

	$objAry = $kapenta->db->getObject($model, $UID);
	$fields64 = $kapenta->db->serialize($objAry);

	$msg = ''
	 . "  <update>\n"
	 . "    <model>" . $model . "</model>\n"
	 . "    <fields>\n"
	 . $fields64
	 . "    </fields>\n"
	 . "  </update>\n";

	//arg: message - XML document [string]
	//arg: exclude - UID fo a P2P_Peer, we don't bounce messages back [string]
	//arg: priority - 0 to 9, default is 9 [string]

	$detail = array(
		'message' => $msg,
		'peer' => $args['peer'],
		'priority' => $args['priority']
	);

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "p2p_narrowcast:\n";
		print_r($detail);
	}

	$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);
}


?>
