<?

	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	raised when a file has been received from a peer
//--------------------------------------------------------------------------------------------------
//arg: module - module this file is managed by [string]
//arg: model - type of object to which file is attached [string]
//arg: UID - UID of object to which file is attached [string]
//arg: fileName - location relative to installPath [string]
//opt: hash - sha1 hash of received file [string]
//opt: peer - peer from which this file was downloaded [string]

function p2p__cb_file_received($args) {
	global $kapenta;
	global $kapenta;
	global $db;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('fileName', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { $args['peer'] = ''; }

	$fileName = $args['fileName'];

	if (false == $kapenta->fs->exists($fileName)) { return false; }

	if (false == array_key_exists('hash', $args)) { $args['hash'] = $kapenta->fileSha1($fileName); }
	

	$hash = $args['hash'];

	$objRef = $args['model'] . '::' . $args['UID'];
	$msg = 'Received file ' . $objRef . ' from ' . $args['peer'];
	$kapenta->logP2P($msg);

	//----------------------------------------------------------------------------------------------
	//	add as a gift for all other peers
	//----------------------------------------------------------------------------------------------
	/* previous version using offer system
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
	*/

	//----------------------------------------------------------------------------------------------
	//	announce file to other peers (message queue)
	//----------------------------------------------------------------------------------------------
	$msg = ''
	 . "<fileoffer>\n"
	 . "  <module>" . $args['module'] . "</module>\n"
	 . "  <model>" . $args['model'] . "</model>\n"
	 . "  <uid>" . $args['UID'] . "</uid>\n"
	 . "  <filename>" . $fileName . "</filename>\n"
	 . "  <hash>" . $hash . "</hash>\n"
	 . "  <peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
	 . "</fileoffer>\n";

	$detail = array(
		'message' => $msg,
		'priority' => '5',
		'exclude' => $args['peer']
	);

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);

	return true;
}

?>
