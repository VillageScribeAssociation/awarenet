<?

//--------------------------------------------------------------------------------------------------
//|	allows other modules to use the p2p event queue to do asynchronous database updates
//--------------------------------------------------------------------------------------------------
//;	Note: this is generally to increase performance of database inserts and updates where timing
//;	is not critical, transactions are not available/possible and race conditions will not be 
//;	present.  It is originally motivated by high latency of MySQL inserts on services such as
//;	Dreamhost - where actions such as broadcast PM can take 30 seconds or more.

//arg: model - type of object to be saved [string]
//arg: data - dict of fields and values [array]
//opt: priority - orders event processing, 0-9, 0 being highest (int) [string]

function p2p__cb_p2p_selfcast($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "received p2p_selfcast event.<br/>\n";
		print_r($args);
	}

	$priority = '2';			//%	priority queue to add this event to (int) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('data', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (true == array_key_exists('priority', $args)) { $priority = $args['priority']; }

	$model = $args['model'];
	$data = $args['data'];

	$peerUID = $kapenta->registry->get('p2p.server.uid');
	if ('' == $peerUID) { $peerUID = 'self'; }

	if (false == $kapenta->db->tableExists($model)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	serialize and queue the event
	//----------------------------------------------------------------------------------------------

	$xml = ''
	 . "  <selfcast>\n"
	 . "    <model>$model</model>\n"
	 . "    <fields64>\n"
	 . $kapenta->db->serialize($data)
	 . "    </fields64>\n"
	 . "  </selfcast>\n";

	list($usec, $sec) = explode(" ", microtime());
	$fileName = 'data/p2p/received/' . $priority . '_' . $peerUID . '_' . $sec . '_' . $usec . '.evt';
	$kapenta->fs->put($fileName, $xml);

}


?>
