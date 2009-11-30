<?

require_once($installPath . 'modules/files/models/files.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted record

function comments__cb_object_deleted($args) {
	global $user;
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any comments owned by this record
	//----------------------------------------------------------------------------------------------

	$contitions = array(	"refUID='" . sqlMarkup($args['UID']) . "'", 
							"refModule='" . sqlMarkup($args['UID']) . "'"	);

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
