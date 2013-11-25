<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a peer requests part of a file
//--------------------------------------------------------------------------------------------------
//arg: module - kapenta module which manages this file [string]
//arg: model - type of object which owns this file [string]
//arg: UID - UID of object which owns this file [string]
//arg: filename - location of thsi file on disk [string]
//arg: filehash - sha1 hash of entire file [string]
//arg: partindex - sequence number of this part, from 0 (int) [string]
//arg: parthash - SHA1 hash of this part
//arg: partsize - length of this part, bytes (int) [string]
//arg: chunksize - maximum / default siize fo parts, kb (int) [string]

function p2p__cb_p2p_requestfilepart_received($args) {
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peer', $args)) { return false; }
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('filename', $args)) { return false; }
	if (false == array_key_exists('filehash', $args)) { return false; }
	if (false == array_key_exists('partindex', $args)) { return false; }
	if (false == array_key_exists('parthash', $args)) { return false; }
	if (false == array_key_exists('partsize', $args)) { return false; }
	if (false == array_key_exists('chunksize', $args)) { return false; }

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "requestfilepart_recieved passed basic tests\n";
	}

	$partIndex = (int)$args['partindex'];

	//----------------------------------------------------------------------------------------------
	//	load the chunk
	//----------------------------------------------------------------------------------------------

	$klf = new KLargeFile($args['filename'], $args['filehash'], $args['uid']);
	$klf->chunkSize = (int)$args['chunksize'];
	$raw = $klf->getPart($partIndex);

	$realHash = sha1($raw);
	if ($realHash != $args['parthash']) {
		if ('yes' == $kapenta->registry->get('p2p.debug')) {
			echo "HASH MISMATCH $realHash != " . $args['parthash'] . "\n";
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	return to peer
	//----------------------------------------------------------------------------------------------

	$msg = ''
	 . "<filepart>\n"
	 . "  <data64>" . base64_encode($raw) . "</data64>\n"
	 . "  <filename>" . $args['filename'] . "</filename>\n"
	 . "  <filehash>" . $args['filehash'] . "</filehash>\n"
	 . "  <partindex>" . $partIndex . "</partindex>\n"
	 . "  <parthash>" . $args['parthash'] . "</parthash>\n"
	 . "  <uid>" . $args['uid'] . "</uid>\n"
	 . "</filepart>\n";

	$detail = array(
		'message' => $msg,
		'peer' => $args['peer'],
		'priority' => '7'
	);

	if ('yes' == $kapenta->registry->get('p2p.debug')) { print_r($detail); }
	$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);

}

?>
