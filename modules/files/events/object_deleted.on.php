<?

require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//-------------------------------------------------------------------------------------------------
//*	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: model - type of object which owned the deleted record
//arg: UID - UID of deleted record

function files__cb_object_deleted($args) {
	global $kapenta;
	global $kapenta;
	global $session;

	if (false == array_key_exists('module', $args)) { return false; }
	//if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any files owned by this record
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['UID']) . "'";
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['module']) . "'";

	$range = $kapenta->db->loadRange('files_file', '*', $conditions, '', '', '');

	foreach($range as $item) {
		$model = new Files_File();
		$model->loadArray($item);
		$report = $model->delete();
		if ('' == $report) {
			$kapenta->session->msg('Deleted file: ' . $item['title'] . ' (' . $item['UID'] . ')');
		} else {
			$kapenta->session->msgAdmin('Could not delete file: '. $item['title'] .' ('. $item['UID'] .')');
		}
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
