<?

require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//-------------------------------------------------------------------------------------------------
//	fired when a record is deleted
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted record
//arg: UID - UID of deleted record

function comments__cb_object_deleted($args) {
	global $db;

	global $user;
	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	delete any comments owned by this record
	//----------------------------------------------------------------------------------------------

	$conditions = array(	"refUID='" . $db->addMarkup($args['UID']) . "'", 
							"refModule='" . $db->addMarkup($args['module']) . "'"	);

	$rows = $db->loadRange('comments_comment', '*', $conditions, '', '', '');

	foreach($rows as $row) {
		$model = new Comments_Comment();
		$model->loadArray($row);
		$model->delete();
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
