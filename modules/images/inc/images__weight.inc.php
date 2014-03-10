<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//*	functions for arranging images by weight (DEPRECATED)
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//|	ensure that all images attached to an item have consecutive weight
//-------------------------------------------------------------------------------------------------

function images__checkWeight($refModule, $refUID) {
	global $kapenta;

	$sql = "select UID, weight from images_image "
		 . "where refUID='" . $kapenta->db->addMarkup($refUID) . "' "
		 . "and refModule='" . $kapenta->db->addMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = $kapenta->db->query($sql);
	$idx = 0;

	//---------------------------------------------------------------------------------------------
	//	ensure that all images are weighted 0-n
	//---------------------------------------------------------------------------------------------

	while ($row = $kapenta->db->fetchAssoc($result)) {			// for all images attached to this item
		$row = $kapenta->db->rmArray($row);
		if ($row['weight'] != $idx) { 				// if this one is not in order
			$model = new Images_Image($row['UID']);		// load record
			$model->weight = $idx;			// set weight to idx
			$model->save();							// save it
		}
		$idx++;		
	}
}

//-------------------------------------------------------------------------------------------------
//	get UID of next heaviest item, false if none (TODO: find only one record, not all)
//-------------------------------------------------------------------------------------------------

function images__getNextHeaviest($refModule, $refUID, $weight) {
	global $kapenta;

	$sql = "select UID, weight from images_image "
		 . "where refUID='" . $kapenta->db->addMarkup($refUID) . "' "
		 . "and refModule='" . $kapenta->db->addMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		if ($row['weight'] > $weight) { return $row['UID']; }
	}
	
	return false;
}

//-------------------------------------------------------------------------------------------------
//	get UID of next heaviest item, false if none (TODO: find only one record, not all)
//-------------------------------------------------------------------------------------------------

function images__getNextLightest($refModule, $refUID, $weight) {
	global $kapenta;

	$sql = "select UID, weight from Images_image "
		 . "where refUID='" . $kapenta->db->addMarkup($refUID) . "' "
		 . "and refModule='" . $kapenta->db->addMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = $kapenta->db->query($sql);
	$retVal = false;

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		if ($row['weight'] < $weight) { $retVal = $row['UID']; }
	}
	
	return $retVal;
}

//-------------------------------------------------------------------------------------------------
//	get weight of heaviest image attached to an item
//-------------------------------------------------------------------------------------------------

function images__getHeaviest($refModule, $refUID) {
	global $kapenta;

	$retVal = 0;

	$sql = "select UID, weight from images_image "
		 . "where refUID='" . $kapenta->db->addMarkup($refUID) . "' "
		 . "and refModule='" . $kapenta->db->addMarkup($refModule) . "' "
		 . "order by floor(weight)";

	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		if ($row['weight'] > $retVal) { $retVal = $row['weight']; }
	}
	
	return $retVal;
}

?>