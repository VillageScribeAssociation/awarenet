<?

//-------------------------------------------------------------------------------------------------
//	functions for arranging images by weight
//-------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	ensure that all images attached to an item have consecutive weight
//-------------------------------------------------------------------------------------------------

function images__checkWeight($refModule, $refUID) {
	$sql = "select UID, weight from images "
		 . "where refUID='" . sqlMarkup($refUID) . "' "
		 . "and refModule='" . sqlMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = dbQuery($sql);
	$idx = 0;

	//---------------------------------------------------------------------------------------------
	//	ensure that all images are weighted 0-n
	//---------------------------------------------------------------------------------------------

	while ($row = dbFetchAssoc($result)) {			// for all images attached to this item
		$row = sqlRMArray($row);
		if ($row['weight'] != $idx) { 				// if this one is not in order
			$model = new Image($row['UID']);		// load record
			$model->data['weight'] = $idx;			// set weight to idx
			$model->save();							// save it
		}
		$idx++;		
	}
}

//-------------------------------------------------------------------------------------------------
//	get UID of next heaviest item, false if none (TODO: find only one record, not all)
//-------------------------------------------------------------------------------------------------

function images__getNextHeaviest($refModule, $refUID, $weight) {
	$sql = "select UID, weight from images "
		 . "where refUID='" . sqlMarkup($refUID) . "' "
		 . "and refModule='" . sqlMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		if ($row['weight'] > $weight) { return $row['UID']; }
	}
	
	return false;
}

//-------------------------------------------------------------------------------------------------
//	get UID of next heaviest item, false if none (TODO: find only one record, not all)
//-------------------------------------------------------------------------------------------------

function images__getNextLightest($refModule, $refUID, $weight) {
	$sql = "select UID, weight from images "
		 . "where refUID='" . sqlMarkup($refUID) . "' "
		 . "and refModule='" . sqlMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = dbQuery($sql);
	$retVal = false;

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		if ($row['weight'] < $weight) { $retVal = $row['UID']; }
	}
	
	return $retVal;
}

//-------------------------------------------------------------------------------------------------
//	get weight of heaviest image attached to an item
//-------------------------------------------------------------------------------------------------

function images__getHeaviest($refModule, $refUID) {
	$retVal = 0;

	$sql = "select UID, weight from images "
		 . "where refUID='" . sqlMarkup($refUID) . "' "
		 . "and refModule='" . sqlMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = dbQuery($sql);

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		if ($row['weight'] > $retVal) { $retVal = $row['weight']; }
	}
	
	return $retVal;
}

?>
