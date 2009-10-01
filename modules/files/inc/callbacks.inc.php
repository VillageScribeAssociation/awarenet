<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['module']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function files__cb_record_delete($args) {
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any announcements belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from files "
		 . "where refUID='". sqlMarkup($args['UID']) ."' "
		 . "and refModule='". sqlMarkup($args['module']) ."'";

	$result = dbQuery($sql);

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$model = new File($row['UID']);
		$_SESSION['sMessage'] .= "Deleted file " . $model->data['title'] . "<br/>\n";
		$model->delete();
	}
}

?>
