<?

//--------------------------------------------------------------------------------------------------
//|	show a page of deleted objects
//--------------------------------------------------------------------------------------------------
//opt: pageNo - results page to display (int) [string]
//opt: objectType - type of deleted object to show, * for all [string]
//opt: num - number of objects per page, default is 50 (int) [string]

function revisions_listdeleted($args) {
	global $db, $user, $theme;
	$html = '';					//%	return value [string]
	$pageNo = 1;				//%	page number (starts at 1) [int]
	$num = 50;					//%	number of items per page [int]
	$objectType = '*';			//%	type of deleted object to show [string]
	$pagination = 'yes';		//%	display pagination links (yes|no) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }

	if ((true == array_key_exists('objectType', $args)) && ('*' != $args['objectType'])) {
		if (false == $db->tableExists($args['objectType'])) { return '(unknown object type)'; }
		$objectType = $args['objectType'];
	}

	//----------------------------------------------------------------------------------------------
	//	count matching objects and load a page of deleted objects from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	if ('*' != $objectType) { $conditions[] = "refModel='" . $db->addMarkup($objectType) . "'"; }
	//TODO: add conditions here

	$totalItems = $db->countRange('revisions_deleted', $conditions);
	$totalPages = ceil($totalItems / $num);

	$start = (($pageNo - 1) * $num);

	$range = $db->loadRange('revisions_deleted', '*', $conditions, 'editedOn', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$table = array();
	$table[] = array('Module', 'Model', 'UID');
	foreach($range as $row) {
		$typeUrl = '%%serverPath%%revisions/listdeleted/type_' . $row['refModel'];
		$typeLink = "<a href='" . $typeUrl . "'>" . $row['refModel'] . "</a>";

		$itemUrl = '%%serverPath%%revisions/showdeleted/' . $row['UID'];
		$itemLink = "<a href='" . $itemUrl . "'>" . $row['refUID'] . "</a>";

		$table[] = array($row['refModule'], $typeLink, $itemLink);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	if ('yes' == $pagination) {
		$paginationBlock = ''
		 . "[[:theme::pagination::page=$pageNo::total=$totalPages::link=/revisions/listdeleted/:]]";

		$pageNav = $theme->expandBlocks($paginationBlock, '');
		$html = $pageNav . $html . $pageNav;
	}

	return $html;
}

?>
