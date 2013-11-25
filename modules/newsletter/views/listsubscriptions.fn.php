<?

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all Subscription objects, paginated
//--------------------------------------------------------------------------------------------------
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]

function newsletter_listsubscriptions($args) {
	global $db, $user, $theme;

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'createdOn';		//%	default list order [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('newsletter', 'newsletter_subscription', 'list')) { return ''; }


	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($kapenta->request->args['by'])) {
		}
	}

	//----------------------------------------------------------------------------------------------
	//	count and load list items
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]
	//add any conditions here, eg: $conditions[] = "published='yes'";

	$start = (($pageNo - 1) * $pageSize);					//% list ordinal of first item [int]	
	$total = $db->countRange('newsletter_subscription', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('newsletter_subscription', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>no Roles yet added</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	$table = array();										//% 2d [array]
	$titleRow = array('address', 'status', '[x]');			//% add column names here [array:string]
	$table[] = $titleRow;

	$model = new Newsletter_Subscription();
	foreach($range as $item) {
		$model->loadArray($item);
		$ext = $model->extArray();

		$row = array(
			$ext['email'],
			$ext['status'],
			$ext['editLinkJs']
		);										//% add columns here [array:string]
		$table[] = $row;
	}

	$link = 'newsletter/listsubscription/';	//% relative to serverPath [string]
	$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
