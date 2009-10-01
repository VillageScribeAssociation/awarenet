<?

//--------------------------------------------------------------------------------------------------
//	utility functions for working with modules
//--------------------------------------------------------------------------------------------------

//==================================================================================================
//	images
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	image associates with a record on a module, returns image record
//--------------------------------------------------------------------------------------------------

function imgGetHeaviest($refModule, $refUID) {	
	$sql = "select * from images "
		 . "where refModule='" . sqlMarkup($refModule) . "'"
		 . " and refUID='" . sqlMarkup($refUID) . "' "
		 . "order by weight DESC limit 1";

	$result = dbQuery($sql);
	if (dbNumRows($result) == false) { return false; }
	$row = dbFetchAssoc($result);
	return sqlRMArray($row);
}

function imgGetHeaviestUrl($refModule, $refUID, $size) {
	$row = imgGetHeaviest($refModule, $refUID);
	if ($row == false) { return '%%serverPath%%/themes/%%%defaultTheme%%/unavailable/' . $size; }
	return '%%serverPath%%/images/' . $size . '/' . $row['recordAlias'];
}

function imgGetHeaviestUID($refModule, $refUID) {
	$row = imgGetHeaviest($refModule, $refUID);
	if ($row == false) { return false; }
	return $row['UID'];
}

function imgDeleteAll($refModule, $refUID) {

}

//==================================================================================================
//	comments
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	
//--------------------------------------------------------------------------------------------------

//==================================================================================================
//	files
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	
//--------------------------------------------------------------------------------------------------

?>
