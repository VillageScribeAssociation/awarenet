<?

//--------------------------------------------------------------------------------------------------
//*	display types and count of deleted objects
//--------------------------------------------------------------------------------------------------

function revisions_listdeletedtypes($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$sql = ''
	 . "SELECT refModel, count(UID) as numObjects "
	 . "FROM revisions_deleted "
	 . "GROUP BY refModel "
	 . "ORDER BY refModel";

	$result = $kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[]  = array('Object Type', 'Count');
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		$listUrl = '%%serverPath%%revisions/listdeleted/type_' . $item['refModel'] . '/';
		$listLink = "<a href='" . $listUrl . "'>" . $item['refModel'] . "</a>";
		$table[] = array($listLink, $item['numObjects']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}


?>
