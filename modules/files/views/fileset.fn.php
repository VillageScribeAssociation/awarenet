<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of files associated with something
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - number of files per page [string]

function files_fileset($args) {
		global $kapenta;
		global $kapenta;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	//$authArgs = array('UID' => $args['refUID']);
	//if (authHas($args['refModule'], 'files', $authArgs) == false) { return false; }
	//TODO: permissions check

	//----------------------------------------------------------------------------------------------
	//	load the file records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from files_file where refModule='" . $kapenta->db->addMarkup($args['refModule']) 
	     . "' and refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	
	//TODO: $db->loadRange

	$result = $kapenta->db->query($sql);
	while($row = $kapenta->db->fetchAssoc($result)) {
		
		$imgUrl = '%%serverPath%%files/thumb/' . $row['alias'];
		$editURL = '%%serverPath%%files/edit/return_uploadmultiple/' . $row['alias'];
		//if (authHas($row['refModule'], 'files', '') == false) {
		//	$editURL = '%%serverPath%%files/viewset/return_uploadmultiple/' . $recordAlias;
		//}
		
		$html .= "<a href='" . $editURL . "'>" 
			. "<img src='" . $imgUrl . "' border='0' /></a>\n";
		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
