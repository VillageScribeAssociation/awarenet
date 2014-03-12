<?

require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is deleted from the database
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted record

function images__cb_object_deleted($args) {
		global $kapenta;
		global $kapenta;
		global $session;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//$kapenta->session->msgAdmin("Deleting images belonging to " . $args['module'] . " " . $args['UID']);

	//----------------------------------------------------------------------------------------------
	//	delete any images owned by this record
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['UID']) . "'";
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['module']) . "'";

	$range = $kapenta->db->loadRange('images_image', '*', $conditions, '', '', '');

	foreach($range as $row) {
		$model = new Images_Image();
		$model->loadArray($row);
		$model->delete();
		//$kapenta->logSync(
		//	"images module is deleting record " . $model->UID . " in response to event<br/>\n"
		//);
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
