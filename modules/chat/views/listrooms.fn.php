<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all Room objects, paginated
//--------------------------------------------------------------------------------------------------
//opt: state - show only rooms with this state [string]
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]

function chat_listrooms($args) {
	global $db, $user, $theme;

	$state = '';				//%	filter to this state is set [string]
	$pageNo = 1;				//%	first page if not specified [int]
	$pageSize = 10;				//%	default number of items per page [int]
	$orderBy = 'createdOn';		//%	default list order [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('chat', 'chat_room', 'list')) { return ''; }

	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }
	if (true == array_key_exists('state', $args)) { $state = $args['state']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($req->args['by'])) {
		}
	}

	//----------------------------------------------------------------------------------------------
	//	count and load list items
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]
	if ('' != $state) { $conditions = array("state='" . $db->addMarkup($state) . "'"); }
	//add any conditions here, eg: $conditions[] = "published='yes'";

	$start = (($pageNo - 1) * $pageSize);					//% list ordinal of first item [int]	
	$total = $db->countRange('chat_room', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('chat_room', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>no known chat rooms</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	$table = array();										//% 2d [array]
	$titleRow = array('Topic', 'Members');					//% add column names here [array:string]
	$table[] = $titleRow;

	$model = new Chat_Room();
	foreach($range as $item) {
		$model->loadArray($item);
		$ext = $model->extArray();

		//% add columns here [array:string]
		$row = array($ext['titleLink'], (string)$model->memberships->count());	

		$table[] = $row;
	}

	$link = 'chat/listroom/';	//% relative to serverPath [string]
	$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
