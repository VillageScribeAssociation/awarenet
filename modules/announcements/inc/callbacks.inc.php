<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['module']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function announcements__cb_record_delete($args) {
	global $db;

	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any announcements belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from Announcements_Announcement "
		 . "where refUID='". $db->addMarkup($args['UID']) ."' "
		 . "and refModule='". $db->addMarkup($args['module']) ."'";

	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Announcements_Announcement($row['UID']);
		$_SESSION['sMessage'] .= "Deleted announcement " . $model->title . "<br/>\n";
		$model->delete();
	}
}

?>