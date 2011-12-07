<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries belonging to a specified user
//--------------------------------------------------------------------------------------------------
//: galleries which do not contain any images are not displayed
//opt: orderBy - field to order by, default is title (title|imagecount|createdOn|editedOn) [string]
//opt: pageNo - page number to display, default is '1' [string]
//opt: pagination - display pagination bars, default is 'yes' (yes|no) [string]
//opt: num - number of items per page (int) [string]
//opt: schoolUID - filter to ggalleries created at this school [string]
//returns: html list [string]

function gallery_summarylist($args) {
	global $db;
	global $theme;

	$pageNo = 1;				//%	page to display, from 1 [int]
	$num = 10;					//%	number of galleries per page [int]
	$start = 0;					//% db results offset [int]
	$orderBy = 'title';			//%	sort field [string]
	$ad = 'DESC';				//%	list order (ASC|DESC) [string]
	$pagination = 'yes';		//%	display pagination bar and order links (yes|no) [string]
	$schoolUID = '';			//%	filter to galleries created at this school [string]
	$html = '';					//%	return value [string]

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('orderBy', $args)) { $orderBy = $args['orderBy']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('pageSize', $args)) { $num = (int)$args['pageSize']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }
	if (true == array_key_exists('schoolUID', $args)) { $schoolUID = $args['schoolUID']; }

	if ($num <= 0) { $num = 10; }

	switch ($orderBy) {
		case 'createdOn':	$ad = 'DESC';	break;
		case 'editedOn':	$ad = 'DESC';	break;
		case 'title':		$ad = 'ASC';	break;
		case 'imagecount':	$ad = 'DESC';	break;
		case 'ownerName':	$ad = 'ASC';	break;
		case 'schoolName':	$ad = 'ASC';	break;
		default: $orderBy = 'createdOn';	break;	// prevent SQL injection
	}

	//---------------------------------------------------------------------------------------------
	//	count galleries, set start and end rows and load the recordset
	//---------------------------------------------------------------------------------------------
	$conditions = array('imagecount > 0');	// do not show galleries with no images
	if ('' != $schoolUID) { $conditions[] = "schoolUID='" . $db->addMarkup($schoolUID) . "'"; }

	$totalItems = $db->countRange('gallery_gallery', $conditions);
	$totalPages = ceil($totalItems / $num);
	$start = $num * ($pageNo - 1);

	$range = $db->loadRange('gallery_gallery', '*', $conditions, $orderBy .' '. $ad, $num, $start);

	//---------------------------------------------------------------------------------------------
	//	make the block
	//---------------------------------------------------------------------------------------------

	$linkBase = '%%serverPath%%gallery/listall/orderby_';
	$pagination = "[[:theme::pagination::page=" . $pageNo 
				 . "::total=" . $totalPages . "::link=" . $linkBase . $orderBy . '/' . ":]]\n";

	$orderLinks = '[[:gallery::orderlinks:]]';

	if ('yes' == $pagination) { $html .= $pagination . $orderLinks; }

	foreach($range as $item) { $html .= "[[:gallery::summary::raUID=" . $item['UID'] . ":]]"; }
	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html .= $pagination . $orderLinks; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

