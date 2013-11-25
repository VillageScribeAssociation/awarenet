<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a peer has sent us a file manifest (list of file parts to be downloaded)
//--------------------------------------------------------------------------------------------------
//arg: rawxml - contains a raw manifest as used by KLargeFile [string]
//arg: path - final location of downloaded file [string]
//arg: hash - sha1 hash of file contents [string]

function p2p__cb_p2p_klargefile_received($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('rawxml', $args)) { return false; }
	if (false == array_key_exists('path', $args)) { return false; }
	if (false == array_key_exists('hash', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	parse the manifest and save to disk
	//----------------------------------------------------------------------------------------------
	$klf = new KLargeFile($args['path'], $args['hash']);
	$check = $klf->loadMetaXml($args['rawxml']);
	if (false == $check) { return false; }
	$klf->saveMetaXml();

	//----------------------------------------------------------------------------------------------
	//	queue all outstanding file parts for download
	//----------------------------------------------------------------------------------------------
	foreach($klf->parts as $idx => $part) {
		if ('pending' == $part['status']) {

			$msg = ''
			 . "<requestfilepart>\n"
			 . "  <module>" . $klf->module . "</module>\n"
			 . "  <model>" . $klf->model . "</model>\n"
			 . "  <uid>" . $klf->UID . "</uid>\n"
			 . "  <filename>" . $klf->path . "</filename>\n"
			 . "  <filehash>" . $klf->hash . "</filehash>\n"
			 . "  <chunksize>" . $klf->chunkSize . "</chunksize>\n"
			 . "  <partindex>" . $idx . "</partindex>\n"
			 . "  <parthash>" . $part['hash'] . "</parthash>\n"
			 . "  <partsize>" . $part['size'] . "</partsize>\n"
			 . "</requestfilepart>\n";

			$detail = array(
				'message' => $msg,
				'peer' => $args['peer'],
				'priority' => '7'
			);

			$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);
		}
	}

}

?>
