<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list UIDs of images owned by some object on a given module
//--------------------------------------------------------------------------------------------------
//arg: refUID - recordAlias or UID of owner record [string]
//arg: refModule - module owner recod belongs to [string]
//opt: limit - maximum number of UIDs to show (integer) [string]
//opt: separator - list separator, default is pipe [string]
//: images are ordered by weight, future versions may have more options if need arises

function images_listuids($args) {
	global $db;
	$limit = '';
	$separator = '|';
	$retVal = array();		//TODO: improve this

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (true == array_key_exists('limit', $args)) { $limit = (int)$args['limit']; }
	if (true == array_key_exists('separator', $args)) { $separator = $args['separator']; }
	
	//---------------------------------------------------------------------------------------------
	//	load UIDs
	//---------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$range = $db->loadRange('images_image', 'UID', $conditions, 'weight', '', $limit);

	//---------------------------------------------------------------------------------------------
	//	return as string
	//---------------------------------------------------------------------------------------------	
	foreach($range as $row) { $retVal[] = $row['UID']; }
	return implode($separator, $retVal);
}

//--------------------------------------------------------------------------------------------------

?>
