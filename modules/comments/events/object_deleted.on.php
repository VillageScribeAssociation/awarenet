<?

require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is deleted ($db->delete())
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

	$conditions = array();
	$conditions[] = "refUID='" . $db->addMarkup($args['UID']) . "'";
	$condiiions[] = "refModule='" . $db->addMarkup($args['module']) . "'";
	$range = $db->loadRange('comments_comment', '*', $conditions, '', '', '');

	foreach($range as $item) {
		$model = new Comments_Comment();
		$model->loadArray($item);
		$model->delete();
	}

	return true;
}


//-------------------------------------------------------------------------------------------------
?>
