<?php

//--------------------------------------------------------------------------------------------------
//*	fired when a peer announces database object which it has shared
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of peer this was received from [string]
//arg: model - type of object [string]
//arg: list - list of UIDs and edit times [string]
//opt: priority - importance of syncing this object (int) [string]

function p2p__cb_p2p_announcement_received($args) {
	global $db;
	global $kapenta;
	global $kapenta;
	global $revisions;

	//----------------------------------------------------------------------------------------------
	//	check the update
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('list', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { return false; }
	if (false == array_key_exists('priority', $args)) { $args['priority'] = '5'; }

	if ('yes' == $kapenta->registry->get('p2p.debug')) { print_r($args); }

	$model = $args['model'];
	$list = $args['list'];

	if (false == $db->tableExists($model)) { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	check all items in the list
	//----------------------------------------------------------------------------------------------
	$lines = explode("\n", $list);
	foreach($lines as $line) {
		if ('' != trim($line)) {
			$want = true;
			$parts = explode('|', $line);
			$UID = trim($parts[0]);
			$editedOn = trim($parts[1]);

			if (true == $db->objectExists($model, $UID)) {
				$objAry = $db->getObject($model, $UID);
				$ourDate = $kapenta->strtotime($objAry['editedOn']);
				$newDate = $kapenta->strtotime($editedOn);
				if ($ourDate >= $newDate) { $want = false; }	//	we have this or a newer version
			}

			if (true == $revisions->isDeleted($model, $UID)) { $want = false; }	//	object is deleted

			if (true == $want) {
				$msg = ''
				 . "  <request>\n"
				 . "    <model>" . $model . "</model>\n"
				 . "    <uid>" . $UID . "</uid>\n"
				 . "  </request>\n";

				$detail = array(
					'peer' => $args['peer'],
					'message' => $msg,
					'priority' => '6'
				);

				$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);
			}
		}
	}

	return true;
}


?>
