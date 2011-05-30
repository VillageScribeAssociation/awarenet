<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['module']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function images__cb_record_delete($args) {
	global $db;


	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any images belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from images_image "
		 . "where refUID='". $db->addMarkup($args['UID']) ."' "
		 . "and refModule='". $db->addMarkup($args['module']) ."'";

	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Images_Image($row['UID']);
		$_SESSION['sMessage'] .= "Deleted image " . $model->title . "<br/>\n";
		$model->delete();
	}
}

?>