<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all thumbnails for a site, module, model or object - paginated
//--------------------------------------------------------------------------------------------------
//opt: refModule - kapenta module name [string]
//opt: refModel - object type [string]
//opt: refModel - object type [string]
//opt: pageNo - page number (integer) [string]
//opt: pageSize - number or items per page (integer) [string]
//opt: orderBy - field to order the list by (defualt is createdOn) [string]
//opt: paginate - use pagination (yes|no) [string]

function images_allthumbs($args) {
	global $req;

	global $db, $user, $theme;

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'title';		//%	default list order [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('images', 'Images_Image', 'list')) { return ''; }

	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $pageSize = (int)$args['pageSize']; }

	// users may list by these fields
	if (true == array_key_exists('by', $args)) {	
		switch(strtolower($req->args['by'])) {
			case 'title':	$orderBy = 'title';		break;
			case 'createdon':	$orderBy = 'createdOn';		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	count and load list items
	//----------------------------------------------------------------------------------------------
	$conditions = array();									//% to filter list by [array:string]

	if (true == array_key_exists('refModule', $args)) 
		{ $conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'"; }

	if (true == array_key_exists('refModel', $args)) 
		{ $conditions[] = "refModel='" . $db->addMarkup($args['refModel']) . "'"; }

	if (true == array_key_exists('refUID', $args)) 
		{ $conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'"; }

	$start = (($pageNo - 1) * $pageSize);					//% list ordinal of first item [int]	
	$total = $db->countRange('Images_Image', $conditions);	//% total number of items [int]
	$totalPages = ceil($total / $pageSize);					//% number of pages [int]
	$range = $db->loadRange('Images_Image', '*', $conditions, $orderBy, $pageSize, $start);

	if (0 == count($range)) {
		$html = "<div class='inlinequote'>(no images)</div><br/>\n";
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//	make the table
	//----------------------------------------------------------------------------------------------
	foreach($range as $item) {
		$html .= "[[:images::thumb::imageUID=" . $item['UID'] . "::link=no:]]\n";
	}

	//$link = 'images/listimage/';	//% relative to serverPath [string]
	//$html .= "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	//$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
