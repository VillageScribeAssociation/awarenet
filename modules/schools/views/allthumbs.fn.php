<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of all schools (thumbnails, no arguments)
//--------------------------------------------------------------------------------------------------

function schools_allthumbs($args) {
	global $serverPath;
	$sql = "select * from images where refModule='schools' order by weight";
	$html = '';
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$thisRa = raGetDefault('schools', $row['refUID']);
			$alt = str_replace('-', ' ', $coinRa);
			$html .= "<a href='/schools/show/" . $thisRa . "'>" 
				. "<img src='/images/thumb90/" . $row['recordAlias'] 
				. "' border='0' alt='" . $alt . "'></a> ";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>