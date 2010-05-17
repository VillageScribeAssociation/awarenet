<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images of all groups (thumbnails, no arguments) TODO: school argument?
//--------------------------------------------------------------------------------------------------

function groups_allthumbs($args) {
	global $serverPath;
	$sql = "select * from images where refModule='groups' order by weight";
	$html = '';
	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$thisRa = raGetDefault('groups', $row['refUID']);
			$alt = str_replace('-', ' ', $coinRa);
			$html .= "<a href='/groups/show/" . $thisRa . "'>" 
				. "<img src='/images/thumb90/" . $row['recordAlias'] 
				. "' border='0' alt='" . $alt . "'></a> ";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

