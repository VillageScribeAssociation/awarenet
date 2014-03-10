<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all Role objects, paginated
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]

function users_listroles($args) {
		global $req;
		global $db;
		global $user;
		global $theme;


	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'name';		//%	default list order [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_role', 'list')) { return ''; }
	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($kapenta->request->args['by'])) {
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
	$total = $db->countRange('users_role', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('users_role', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>no Roles yet added</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	$table = array();										//% 2d [array]
	$titleRow = array();									//% add column names here [array:string]
	$titleRow[] = 'name';
	$titleRow[] = '[x]';
	$table[] = $titleRow;

	$model = new Users_Role();
	foreach($range as $item) {
		$model->loadArray($item);
		$ext = $model->extArray();
		$row = array();										//% add columns here [array:string]
		$row[] = $ext['goLink'];
		$row[] = $ext['delLink'];
		$table[] = $row;
	}

	$link = 'users/listrole/';	//% relative to serverPath [string]
	$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
