<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all Badge objects, paginated
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]

function badges_listbadges($args) {
	global $db, $user, $theme;

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'createdOn';		//%	default list order [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('badges', 'Badges_Badge', 'list')) { return ''; }


	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($req->args['by'])) {
			case 'createdon':	$orderBy = 'createdOn';		break;
			case 'editedon':	$orderBy = 'editedOn';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	count and load list items
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]
	//add any conditions here, eg: $conditions[] = "published='yes'";

	$start = (($pageNo - 1) * $pageSize);					//% list ordinal of first item [int]	
	$total = $db->countRange('Badges_Badge', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('Badges_Badge', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>(no badges yet added)</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	$table = array();										//% 2d [array]
	$titleRow = array('Img', 'Name');						//% add column names here [array:string]
	$table[] = $titleRow;

	$imgOpts = "::refModule=badges::refModel=Badges_Badge::size=thumb::link=no";

	$model = new Badges_Badge();
	foreach($range as $item) {
		$model->loadArray($item);
		$ext = $model->extArray();
		$imgBlock = '[[:images::default' . $imgOpts . '::refUID=' . $ext['UID'] . ':]]';
		$row = array($imgBlock, $ext['nameLink']);			//% add columns here [array:string]
		$table[] = $row;
	}

	$link = 'badges/listbadge/';	//% relative to serverPath [string]
	$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
