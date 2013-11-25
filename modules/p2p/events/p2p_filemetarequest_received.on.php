<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a peer requests file metadata from us
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of a P2P_Peer object to return the meta document to [string]
//arg: model - type of object which owns this file [string]
//arg: uid - UID of object which owns this file [string]
//arg: filename - location of this file in ~/data/
//arg: hash - Sha1 hash of file [string]

function p2p__cb_p2p_filemetarequest_received($args) {
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and file
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peer', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('filename', $args)) { return false; }
	if (false == array_key_exists('hash', $args)) { return false; }

	//TODO: load database object, check fileName and hash fields on it

	//TODO: check the hash on disk

	$fileName = $args['filename'];

	if (false == $kapenta->fs->exists($fileName)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the file manifest
	//----------------------------------------------------------------------------------------------

	$klf = new KLargeFile($fileName);
	$check = $klf->makeFromFile();
	if (false == $check) { return false; }

	$detail = array(
		'message' => $klf->toXml(),
		'peer' => $args['peer'],
		'priority' => '5'
	);

	$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);

	return true;
}

?>
