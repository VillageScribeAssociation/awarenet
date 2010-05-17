<?

require_once($installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted record

function images__cb_object_deleted($args) {
	global $user;
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any images owned by this record
	//----------------------------------------------------------------------------------------------

	$conditions = array(	"refUID='" . sqlMarkup($args['UID']) . "'", 
							"refModule='" . sqlMarkup($args['module']) . "'"	);

	$rows = dbLoadRange('images', '*', $conditions, '', '', '');

	foreach($rows as $row) {
		$model = new Image();
		$model->loadArray($row);
		$model->delete();
		logSync("images module is deleting record " . $model->data['UID'] . " in response to event<br/>\n");
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
