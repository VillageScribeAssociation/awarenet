<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['module']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function images__cb_record_delete($args) {

	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any images belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from images "
		 . "where refUID='". sqlMarkup($args['UID']) ."' "
		 . "and refModule='". sqlMarkup($args['module']) ."'";

	$result = dbQuery($sql);

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$model = new Image($row['UID']);
		$_SESSION['sMessage'] .= "Deleted image " . $model->data['title'] . "<br/>\n";
		$model->delete();
	}
}

?>
