<?

	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is received from another peer
//--------------------------------------------------------------------------------------------------
//+	We can now pass this on to other peers.  Assume all peers except the one we got it from
//+	may want the item.

//arg: type - must be 'object' [string]
//arg: model - type of object we received [string]
//arg: UID - UID of object or file owner [string]
//opt: properties - array of fields and values which make up this object [string]
//opt: peer - UID of peer we received this from [string]

function p2p__cb_object_received($args) {
	global $kapenta;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('type', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('properties', $args)) { return false; }
	if ('object' != $args['type']) { return false; }
	if (false == array_key_exists('peer', $args)) { $args['peer'] = ''; }
	if (false == is_array($args['properties'])) { return false ; }

	$objRef = $args['model'] . '::' . $args['UID'];
	$msg = 'Received object ' . $objRef . ' from ' . $args['peer'];
	$kapenta->logP2P($msg);

	//----------------------------------------------------------------------------------------------
	//	notify any other peers
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('p2p_peer', '*');
	foreach($range as $item) {
		if ($item['UID'] != $args['peer']) {
			$offers = new P2P_Offers($peer->UID);
			$offers->updateObject($args['model'], $args['UID'], $args['properties']);

			$msg = ''
			 . 'Noting new/updated object ' . $objRef . ' for peer '
			 . $item['name'] . '(' . $item['UID'] . ')';

			$kapenta->logP2P($msg);
		}
	}

	return true;
}


?>
