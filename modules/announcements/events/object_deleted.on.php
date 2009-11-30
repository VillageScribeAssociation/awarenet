<?

require_once($installPath . 'modules/announcements/models/announcements.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when an object is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted object

function comments__cb_object_deleted($args) {
	global $user;
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//---------------------------------------------------------------------------------------------
	//	delete any comments owned by this record
	//---------------------------------------------------------------------------------------------

	$contitions = array(	"refUID='" . sqlMarkup($args['UID']) . "'", 
							"refModule='" . sqlMarkup($args['module']) . "'"	);

	$rows = dbLoadRange('announcement', '*', $conditions, '', '', '');

	foreach($rows as $row) {
		$model = new Announcement();
		$model->loadArray($row);
		$model->delete();
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
