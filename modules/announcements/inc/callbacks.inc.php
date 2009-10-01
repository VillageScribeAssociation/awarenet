<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['module']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function announcements__cb_record_delete($args) {
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any announcements belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from announcements "
		 . "where refUID='". sqlMarkup($args['UID']) ."' "
		 . "and refModule='". sqlMarkup($args['module']) ."'";

	$result = dbQuery($sql);

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$model = new Announcement($row['UID']);
		$_SESSION['sMessage'] .= "Deleted announcement " . $model->data['title'] . "<br/>\n";
		$model->delete();
	}
}

?>
