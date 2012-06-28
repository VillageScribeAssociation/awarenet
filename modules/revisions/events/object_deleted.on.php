<?

//--------------------------------------------------------------------------------------------------
//|	record object deletions
//--------------------------------------------------------------------------------------------------

function revisions__cb_object_deleted($args) {
	global $db;
	global $session;
	global $revisions;

	//echo "caught event: object_deleted <br/>\n";

	if (false == array_key_exists('data', $args)) {
		$session->msgAdmin('Record deletion: Missing object data argument.');
		return false;
	}

	if (false == array_key_exists('dbSchema', $args)) {
		$session->msgAdmin('Record deletion: Missing object data argument.');
		return false;
	}

	if (false == array_key_exists('isShared', $args)) { $args['isShared'] = true; }

	$data = $args['data'];
	$dbSchema = $args['dbSchema'];

	//$session->msgAdmin("caught event: object_deleted, arguments check out...");

	//------------------------------------------------------------------------------------------
	//	copy to recycle bin (unless set to 'no' archive)	//TODO: move this to /revisions/
	//------------------------------------------------------------------------------------------
	if ((false == array_key_exists('archive', $dbSchema)) || ('no' != $dbSchema['archive'])) {
		foreach($data as $name => $value) { $data[$name] = $db->addMarkup($value); }
		$revisions->recordDeletion($data, $dbSchema, $args['isShared']);
		//$session->msgAdmin("recording deletion of " . $dbSchema['model'] . '::' . $data['UID']);
	}

}

?>
