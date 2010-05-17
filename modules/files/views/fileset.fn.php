<?

	require_once($installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of files associated with something
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - number of files per page [string]

function files_fileset($args) {
	global $serverPath;
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'files', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the file records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from files where refModule='" . sqlMarkup($args['refModule']) 
	     . "' and refUID='" . sqlMarkup($args['refUID']) . "'";
	     
	$html = '';
	$result = dbQuery($sql);
	while($row = dbFetchAssoc($result)) {
		
		$imgUrl = $serverPath . 'files/thumb/' . $row['recordAlias'];
		$editURL = $serverPath . 'files/edit/return_uploadmultiple/' . $row['recordAlias'];
		if (authHas($row['refModule'], 'files', '') == false) {
			$editURL = $serverPath . 'files/viewset/return_uploadmultiple/' . $recordAlias;
		}
		
		$html .= "<a href='" . $editURL . "'>" 
			. "<img src='" . $imgUrl . "' border='0' /></a>\n";
		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

