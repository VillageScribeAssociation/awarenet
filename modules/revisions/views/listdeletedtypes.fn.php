<?

//--------------------------------------------------------------------------------------------------
//*	display types and count of deleted objects
//--------------------------------------------------------------------------------------------------

function revisions_listdeletedtypes($args) {
	global $user;
	global $db;
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$sql = ''
	 . "SELECT refModel, count(UID) as numObjects "
	 . "FROM revisions_deleted "
	 . "GROUP BY refModel "
	 . "ORDER BY refModel";

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[]  = array('Object Type', 'Count');
	while ($row = $db->fetchAssoc($result)) {
		$item = $db->rmArray($row);
		$listUrl = '%%serverPath%%revisions/listdeleted/type_' . $item['refModel'] . '/';
		$listLink = "<a href='" . $listUrl . "'>" . $item['refModel'] . "</a>";
		$table[] = array($listLink, $item['numObjects']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}


?>
