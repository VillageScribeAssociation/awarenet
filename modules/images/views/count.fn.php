<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count images belonging to a given UID on some module specified
//--------------------------------------------------------------------------------------------------
//arg: refModule - module owner recod belongs to [string]
//arg: refModel - type of object which may own images [string]
//arg: refUID - alias or UID of owbject which may own images [string]

function images_count($args) {
	global $db;
	$return = '';

	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }

	//TODO: $db->countRange
	$sql = "SELECT count(UID) as countImages FROM images_image "
		 . "WHERE refUID='" . $db->addMarkup($args['refUID']) . "' "
		 . "AND refModule='" . $db->addMarkup($args['refModule']) . "'";

	$result = $db->query($sql);
	$row = $db->fetchAssoc($result);
	
	return $row['countImages'];
}

//--------------------------------------------------------------------------------------------------

?>
