<?php

//--------------------------------------------------------------------------------------------------
//|	fired when a peer announces it has a file to share with us
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of peer this was received from [string]
//arg: refModule - module this file is owned by [string]
//arg: refModel - type of object which owns this file [string]
//arg: refUID - UID of object which owns this file [string]
//arg: fileName - type of object [string]
//arg: hash - sha1 hash of file [string]

function p2p__cb_p2p_fileoffer_received($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('filename', $args)) { return false; }
	if (false == array_key_exists('hash', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { return false; }

	$fileName = $args['filename'];
	$hash = $args['hash'];
	$peer = $args['peer'];

	$metaFile = 'data/p2p/transfer/meta/' . $hash . '.xml.php';

	//if ((true == $kapenta->fs->exists($fileName)) && ($hash == $kapenta->fileSha1($fileName))) {
	if (true == $kapenta->fs->exists($fileName)) {
		//	we already have this file
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	we don't have this file, check if we're already downloading it
	//----------------------------------------------------------------------------------------------
	if (true == $kapenta->fs->exists($metaFile)) { return true; }

	//----------------------------------------------------------------------------------------------
	//	we don't have this file, request meta from peer
	//----------------------------------------------------------------------------------------------
	$msg = ''
	 . "<filemetarequest>\n"
	 . "  <model>" . $args['model'] . "</model>\n"
	 . "  <uid>" . $args['uid'] . "</uid>\n"
	 . "  <filename>" . $fileName . "</filename>\n"
	 . "  <hash>" . $hash . "</hash>\n"
	 . "</filemetarequest>\n";

	$detail= array(
		'message' => $msg,
		'peer' => $peer,
		'priority' => '5'
	);

	$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);

}


?>
