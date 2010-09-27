<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count images belonging to a given UID on some module specified
//--------------------------------------------------------------------------------------------------
//arg: refUID - recordAlias or UID of owner record [string]
//arg: refModule - module owner recod belongs to [string]

function images_count($args) {
	global $db;
	$return = '';

	if (false == array_key_exists('refUID', $args)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return ''; }

	//TODO: $db->countRange
	$sql = "SELECT count(UID) as countImages FROM Images_Image "
		 . "WHERE refUID='" . $db->addMarkup($args['refUID']) . "' "
		 . "AND refModule='" . $db->addMarkup($args['refModule']) . "'";

	$result = $db->query($sql);
	$row = $db->fetchAssoc($result);
	
	return $row['countImages'];
}

//--------------------------------------------------------------------------------------------------

?>
