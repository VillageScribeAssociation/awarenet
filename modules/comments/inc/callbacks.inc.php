<?

//--------------------------------------------------------------------------------------------------
//	callbacks allow modules to interact with being necessarily dependant on one another
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/comments/models/comments.mod.php');

//--------------------------------------------------------------------------------------------------
//	when a record is deleted on another module
//--------------------------------------------------------------------------------------------------
// * args['table']  = table the record belonged to
// * args['UID'] = UID of the record which was deleted

function comments__cb_record_delete($args) {
	global $db;

	if (array_key_exists('module', $args) == false) { return false; }
	if (array_key_exists('UID', $args) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover if any images belonging this record and delete them
	//----------------------------------------------------------------------------------------------

	$sql = "select UID from comments_comment "
		 . "where refUID='". $db->addMarkup($args['UID']) ."' "
		 . "and refModule='". $db->addMarkup($args['module']) ."'";

	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Comments_Comment($row['UID']);
		$userName = "";
		$_SESSION['sMessage'] .= "Deleted comment posted on " . $model->createdOn . " by "
							   . "[[:users::name::" . $model->createdBy . ":]] <br/>\n";

		$model->delete();
	}
}

?>