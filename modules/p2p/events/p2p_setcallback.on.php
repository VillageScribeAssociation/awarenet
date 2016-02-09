<?php

//--------------------------------------------------------------------------------------------------
//|	create an asynchronous event to be deferred to the background process
//--------------------------------------------------------------------------------------------------
//arg: target - module name or * [string]
//arg: event - name of event to raise [string]
//arg: arsg64 - base64 encoding of arguments [string]

function p2p__cb_p2p_setcallback($args) {
	global $kapenta;	
	$priority = 2;
	$peerUID = $kapenta->registry->get('p2p.server.uid');
	
	if ('' == $peerUID) { $peerUID = 'self'; }
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('target', $args)) { return false; }
	if (false == array_key_exists('event', $args)) { return false; }
	if (false == array_key_exists('args64', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	serialize and queue the event
	//----------------------------------------------------------------------------------------------

	$xml = ''
	 . "  <callback>\n"
	 . "    <target>" . $args['target'] . "</target>\n"
	 . "    <event>" . $args['event'] . "</event>\n"
	 . "    <args64>\n"	
	 . $args['args64']
	 . "    </args64>\n"
	 . "  </callback>\n";

	list($usec, $sec) = explode(" ", microtime());
	$fileName = 'data/p2p/received/' . $priority . '_' . $peerUID . '_' . $sec . '_' . $usec . '.evt';
	$kapenta->fs->put($fileName, $xml);
	
	return true;
}

?>
