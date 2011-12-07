<?

	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	raised when a file has been received from a peer
//--------------------------------------------------------------------------------------------------
//arg: model - type of object to which file is attached [string]
//arg: UID - UID of object to which file is attached [string]
//arg: fileName - location relative to installPath [string]
//opt: peer - peer from which this file was downloaded [string]

function p2p__cb_file_received($args) {
	global $kapenta;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('fileName', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { $args['peer'] = ''; }

	$objRef = $args['model'] . '::' . $args['UID'];
	$msg = 'Received file ' . $objRef . ' from ' . $args['peer'];
	$kapenta->logP2P($msg);

	//----------------------------------------------------------------------------------------------
	//	add as a gift for all other peers
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('p2p_peer', '*', '');
	foreach($range as $item) {
		if ($item['UID'] != $args['peer']) {
			$offers = new P2P_Offers($item['UID']);
			$offers->updateFile($args['model'], $args['UID'], $args['fileName']);

			$msg = ''
			 . 'Noting new/updated file ' . $args['fileName'] . ' for peer '
			 . $item['name'] . '(' . $item['UID'] . ')';

			$kapenta->logP2P($msg);
		}
	}

	return true;
}

?>
