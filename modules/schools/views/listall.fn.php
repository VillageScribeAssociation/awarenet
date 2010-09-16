<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]

function schools_listall($args) {
	global $req;

	global $db, $user, $theme;

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'name';		//%	default list order [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('schools', 'Schools_School', 'list')) { return ''; }


	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($req->args['by'])) {
			case 'name':	$orderBy = 'name';		break;
			case 'createdon':	$orderBy = 'createdOn';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	count and load list items
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]
	//add any conditions here, eg: $conditions[] = "published='yes'";

	$start = (($pageNo - 1) * $pageSize);					//% list ordinal of first item [int]	
	$total = $db->countRange('Schools_School', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('Schools_School', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>no Roles yet added</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	$table = array();										//% 2d [array]
	$titleRow = array();									//% add column names here [array:string]
	$table[] = $titleRow;

	$link = 'schools/listschool/';							//% relative to serverPath [string]
	$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";

	$model = new Schools_School();
	foreach($range as $item) {
		$html .= "[[:schools::summary::raUID=" . $item['UID'] . ":]]";
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;

	//----------------------------------------------------------------------------------------------
	//	previous block
	//----------------------------------------------------------------------------------------------

	//$sql = "select * from schools order by name";
	//$result = $db->query($sql);
	//$html = '';
	//while ($row = $db->fetchAssoc($result)) {
	//	$html .= "[[:schools::summary::raUID=" . $row['UID'] . ":]]";
	//}
	//return $html;
}

//--------------------------------------------------------------------------------------------------

?>