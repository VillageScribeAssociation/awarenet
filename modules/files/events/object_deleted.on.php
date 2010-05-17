<?

require_once($installPath . 'modules/files/models/file.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted record

function files__cb_object_deleted($args) {
	global $user;
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any comments owned by this record
	//----------------------------------------------------------------------------------------------

	$conditions = array(	"refUID='" . sqlMarkup($args['UID']) . "'", 
							"refModule='" . sqlMarkup($args['module']) . "'"	);

	$rows = dbLoadRange('files', '*', $conditions, '', '', '');

	foreach($rows as $row) {
		$model = new File();
		$model->loadArray($row);
		$model->delete();
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
