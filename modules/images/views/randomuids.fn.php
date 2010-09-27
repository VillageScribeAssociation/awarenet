<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list UIDs of random images owned by some object on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - recordAlias or UID of owner record [string]
//arg: refModule - module owner recod belongs to [string]
//opt: limit - maximum number of UIDs to show (integer) [string]
//opt: separator - list separator, default is pipe [string]
//: images are ordered by weight, future versions may have more options if need arises

function images_randomuids($args) {
	global $db;
	//TODO: remove this
	$limit = ''; $separator = '|'; $retVal = array();

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('separator', $args) == true) { $separator = $args['separator']; }
	if (array_key_exists('limit', $args) == true) { $limit = (int)$args['limit']; }
	
	//---------------------------------------------------------------------------------------------
	//	load UIDs
	//---------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";

	$range = $db->loadRange('Images_Image', 'UID', $conditions, 'RAND()', $limit, '');
	//---------------------------------------------------------------------------------------------
	//	return as string
	//---------------------------------------------------------------------------------------------
	
	foreach($range as $row) { $retVal[] = $row['UID']; }
	return implode($separator, $retVal);
}

//--------------------------------------------------------------------------------------------------

?>
