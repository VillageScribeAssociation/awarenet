<?

	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//arg: refModule - module which owns the object to which a file is attached [string]
//arg: refModel - type of object to which file is attached [string]
//arg: refUID - UID of owner [string]
//arg: fileName - location of uploaded file, relative to installPath [string]
//opt: hash - sha1 hash of file [string]
//opt: size - in bytes (int) [string]

function p2p__cb_file_added($args) {
	global $kapenta;
	global $db; 
	global $user;
	global $page;
	global $session;
	global $revisions;

	//----------------------------------------------------------------------------------------------
	//	check arguments and share status
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('fileName', $args)) { return false; }

	if (false == $kapenta->fileExists($args['fileName'])) {
		$session->msg("File added, but filename not passed to event.");
		return false;
	}

	if (false == array_key_exists('hash', $args)) {
		$args['hash'] = sha1_file($kapenta->installPath . $args['fileName']);
	}

	if (false == array_key_exists('size', $args)) {
		$args['size'] = filesize($kapenta->installPath . $args['fileName']);
	}

	if (false == $db->objectExists($args['refModel'], $args['refUID'])) {
		$session->msg("File added, but owner object does not exist.");
		return false;
	}

	if (false == $db->isShared($args['refModel'], $args['refUID'])) {
		$session->msg("File added, but owner object is not shared.");
		return false;
	}

	if (true == $revisions->isDeleted($args['refModel'], $args['refUID'])) {
		$session->msg("File added, but owner object is deleted.");
		return false;
	}

	//$session->msgAdmin("File added, sharing with peers (all tests passed).");

	//----------------------------------------------------------------------------------------------
	//	assume all peers want to know about it (if owned by a valid, shared object)
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('p2p_peer', '*', '');
	$set = new P2P_Offers();

	foreach($range as $item) {
		$msg = 'Sharing new file: ' . $args['fileName'] . ' with ' . $item['name'];
		$session->msgAdmin($msg);

		$set->peerUID = $item['UID'];
		$set->updateFile($args['refModel'], $args['refUID'], $args['fileName']);
	}

	return true;
}

//--------------------------------------------------------------------------------------------------

?>
