<?php

//--------------------------------------------------------------------------------------------------
//*	fired when some database object is received from another peer
//--------------------------------------------------------------------------------------------------
//arg: peer - UID of peer this was received from [string]
//arg: model - type of object [string]
//arg: fields - base64 encoded field set [string]

function p2p__cb_p2p_update_received($args) {
	global $db;
	global $kapenta;
	global $kapenta;
	global $revisions;

	//----------------------------------------------------------------------------------------------
	//	check the update
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('fields', $args)) { return false; }
	if (false == array_key_exists('peer', $args)) { $args['peer'] = ''; }
	if (false == array_key_exists('priority', $args)) { $args['priority'] = '5'; }

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		print_r($args);
	}

	$model = $args['model'];
	$fields64 = $args['fields'];
	$fields = $db->unserialize($fields64);

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "$model:\n";
		print_r($fields);
	}
	
	//----------------------------------------------------------------------------------------------
	//	check if we need / want this update
	//----------------------------------------------------------------------------------------------
	
	if (false == $db->tableExists($model)) { return false; }
	if (false == array_key_exists('UID', $fields)) { return false; }
	if (false == array_key_exists('editedOn', $fields)) { return false; }

	if (true == $db->objectExists($model, $fields['UID'])) {
		$objAry = $db->getObject($model, $fields['UID']);
		if (count($objAry) == 0) { return false; }								//	database error
		if (false == array_key_exists('editedOn', $objAry)) { return false; }	//	invalid

		$ourDate = $kapenta->strtotime($objAry['editedOn']);
		$newDate = $kapenta->strtotime($fields['editedOn']);

		if ($ourDate >= $newDate) { return false; }		//	we already have this or a newer version
	}

	if (true == $revisions->isDeleted($args['model'], $fields['UID'])) { return false; }

	//----------------------------------------------------------------------------------------------
	//	tell other peers about it
	//----------------------------------------------------------------------------------------------

	$msg = ''
	 . "  <update>\n"
	 . "    <model>" . $model . "</model>\n"
	 . "    <fields>\n"
	 . $fields64
	 . "    </fields>\n"
	 . "  </update>\n";

	//arg: message - XML document [string]
	//arg: exclude - UID fo a P2P_Peer, we don't bounce messages back [string]
	//arg: priority - 0 to 9, default is 9 [string]

	$detail = array(
		'message' => $msg,
		'exclude' => $args['peer'],
		'priority' => $args['priority']
	);

	if ('yes' == $kapenta->registry->get('p2p.debug')) {
		echo "p2p_broadcast:\n";
		print_r($detail);
	}

	$kapenta->raiseEvent('p2p', 'p2p_broadcast', $detail);

	//----------------------------------------------------------------------------------------------
	//	store in database, quietly, not raising object_updated event
	//----------------------------------------------------------------------------------------------
	$dbSchema = $db->getSchema($model);
	if (false == array_key_exists('prikey', $dbSchema)) { $dbSchema['prikey'] = ''; }
	$check = false;

	if (true == $db->objectExists($model, $fields['UID'])) {

		$setter = array();
		foreach ($fields as $fieldName => $value) {
			$fVal = $db->addMarkup($value);

			if (
				(true == array_key_exists($fieldName, $dbSchema['fields'])) &&
				(true == $db->quoteType($dbSchema['fields'][$fieldName])) 
			) { $fVal = "\"" . $fVal . "\""; }

			$setter[] = "`" . $fieldName . "`=" . $fVal;
		}

		$sql = ''
		 . "UPDATE $model SET "
		 . implode(", ", $setter)
		 . " WHERE UID='" . $db->addMarkup($fields['UID']) . "'";

		if ('yes' == $kapenta->registry->get('p2p.debug')) { echo $sql . "\n\n"; }

		$db->query($sql);
		//TODO: check for database error her.

	} else {

		if ('yes' == $kapenta->registry->get('p2p.debug')) {
			echo "database schema:\n";
			print_r($dbSchema);
		}

		$newFields = array();
		foreach ($dbSchema['fields'] as $fName => $fType) {
			$value = '';
			if (true == array_key_exists($fName, $fields))
				{ $value = $db->addMarkup($fields[$fName]); }		// prevent SQL injection
			if (true == $db->quoteType($fType)) 
				{ $value = "\"" . $value . "\""; }					// quote string values

			if ($fName !== $dbSchema['prikey']) {
				$newFields[$fName] = $value;
			}
		}

		// assemble the query
		$sql = ''
		 . "INSERT INTO ". $dbSchema['model']
		 . " (" . implode(', ', array_keys($newFields)) . ")"
		 . " VALUES"
		 . " (" . implode(', ', $newFields) . ");";

		if ('yes' == $kapenta->registry->get('p2p.debug')) {
			echo $sql . "\n\n";
		}

		$db->query($sql);
		//TODO: check for database error here

		//-----------------------------------------------------------------------------------------
		//	update this object in memcache
		//-----------------------------------------------------------------------------------------

		$cacheKey = $model . '::' . $fields['UID'];

		$kapenta->cacheSet($cacheKey, serialize($fields));
		
		if (true == array_key_exists('alias', $fields)) {
			$aliasKey = 'alias::' . $model . '::' . strtolower($fields['alias']);
			$kapenta->cacheSet($aliasKey, $fields['UID']);
		}

		//-----------------------------------------------------------------------------------------
		//	raise p2p_update_applied for other modules
		//-----------------------------------------------------------------------------------------

		$detail = array(
			'peer' => $args['peer'],
			'model' => $model,
			'UID' => $fields['UID'],
			'fields' => $fields 
		);

		if ('yes' == $kapenta->registry->get('p2p.debug')) {
			echo "Raising p2p_update_applied event<br/>\n";
			print_r($detail);
			echo "<br/>\n";
		}

		$kapenta->raiseEvent('*', 'p2p_update_applied', $detail);

		//-----------------------------------------------------------------------------------------
		//	invalidate this in view caches
		//-----------------------------------------------------------------------------------------

		$detail = array(
			'model' => $model,
			'UID' => $fields['UID'],
			'data' => $fields 
		);

		$kapenta->raiseEvent('*', 'cache_invalidate', $detail);
		
	}

	return true;
}

?>
