<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	part of a file has been received from a peer
//--------------------------------------------------------------------------------------------------
//arg: uid - UID of owner object [string]
//arg: filename - canonical location of the file this part belongs to [string]
//arg: filehash - hash of the complete file [string]
//arg: partindex - sequence number of this part [string]
//arg: parthash - sequence number of this part [string]
//arg: data64 - base64 encoded file part [string]

function p2p__cb_p2p_filepart_received($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $args)) { return false; }
	if (false == array_key_exists('filename', $args)) { return false; }
	if (false == array_key_exists('filehash', $args)) { return false; }
	if (false == array_key_exists('partindex', $args)) { return false; }
	if (false == array_key_exists('parthash', $args)) { return false; }
	if (false == array_key_exists('data64', $args)) { return false; }

	echo "p2p_filepart_received passed basic tests\n";

	//----------------------------------------------------------------------------------------------
	//	save the chunk
	//----------------------------------------------------------------------------------------------

	$klf = new KLargeFile($args['filename'], $args['filehash'], $args['uid']);
	$stored = $klf->storePart($args['partindex'], $args['data64'], $args['parthash']);

	if (false == $stored) {
		echo "part could not be stored\n";
		return false;
	}		//	disk full?


	$klf->saveMetaXml();						//	record that part is added

	$complete = $klf->checkCompletion();

	if (true == $complete) {
		$check = $klf->stitchTogether();
		if (false == $check) { 
			echo "could not stitch together\n";
			return false;
		}
		$klf->delete();
		echo "deleted manifest\n";
	}

	return true;
}

?>
