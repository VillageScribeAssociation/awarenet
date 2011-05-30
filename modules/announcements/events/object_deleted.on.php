<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function announcements__cb_object_deleted($args) {
	global $db, $user;
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//---------------------------------------------------------------------------------------------
	//	delete any announcements owned by this record
	//---------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['UID']) . "'";
	$conditions[] = "refModule='" . $db->addMarkup($args['module']) . "'";
	$range = $db->loadRange('announcements_announcement', '*', $conditions, '', '', '');

	foreach($range as $row) {
		$model = new Announcements_Announcement();
		$model->loadArray($row);
		$model->delete();
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
