<?

require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: model - type of object which owned the deleted record
//arg: UID - UID of deleted record

function files__cb_object_deleted($args) {
	global $db, $user;

	if (false == array_key_exists('module', $args)) { return false; }
	//if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any comments owned by this record
	//----------------------------------------------------------------------------------------------

	$conditions = array()
	$conditions[] = "refUID='" . $db->addMarkup($args['UID']) . "'"
	$conditions[] = "refModule='" . $db->addMarkup($args['module']) . "'"

	$range = $db->loadRange('Files_File', '*', $conditions, '', '', '');

	foreach($range as $row) {
		$model = new Files_File();
		$model->loadArray($row);
		$model->delete();
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
