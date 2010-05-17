<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of images associated with something
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item which owns the images [string]

function images_imageset($args) {
	global $serverPath;
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'images', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the image records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images where refModule='" . sqlMarkup($args['refModule']) 
	     . "' and refUID='" . sqlMarkup($args['refUID']) . "'";
	     
	$html = '';
	$result = dbQuery($sql);
	while($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$imgUrl = $serverPath . 'images/thumb/' . $row['recordAlias'];
		$editURL = $serverPath . 'images/edit/return_uploadmultiple/' . $row['recordAlias'];
		if (authHas($row['refModule'], 'images', '') == false) {
			$editURL = $serverPath . 'images/viewset/return_uploadmultiple/' . $recordAlias;
		}
		
		$html .= "<a href='" . $editURL . "'>" 
			. "<img src='" . $imgUrl . "' border='0' /></a>\n";
		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

