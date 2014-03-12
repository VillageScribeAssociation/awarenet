<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is deleted ($kapenta->db->delete())
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record [string]
//arg: model - type of object which was deleted [string]
//arg: UID - UID of deleted object [string]

function notifications__cb_object_deleted($args) {
	global $kapenta;
	global $kapenta;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any notifications owned by this record
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['UID']) . "'";
	$condiiions[] = "refModule='" . $kapenta->db->addMarkup($args['module']) . "'";
	$range = $kapenta->db->loadRange('notifications_notification', '*', $conditions, '', '', '');

	foreach($range as $item) {
		$model = new Notifications_Notification();
		$model->loadArray($item);
		$model->delete();
	}

	return true;
}

//-------------------------------------------------------------------------------------------------

?>
