<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries belonging to a specified user
//--------------------------------------------------------------------------------------------------
//: galleries which do not contain any videos are not displayed
//opt: orderBy - field to order by, default is title (title|imagecount|createdOn|editedOn) [string]
//opt: pageNo - page number to display, default is '1' [string]
//opt: num - number of records per page, default '10' [string]
//opt: pagination - show order links and pagination, default is 'yes' (yes|no) [string]
//returns: html list [string]

function videos_summarylist($args) {
	global $kapenta;
	global $theme;

	$pageNo = 1;					//%	page to show, starting from 1 [int]
	$num = 10;						//%	number of items per page [int]
	$start = 0;						//%	offset in database results [int]
	$orderBy = 'editedOn';			//%	order field [string]
	$ad = 'DESC';					//%	list order [string]
	$pagination = 'yes';			//%	show order links and pagination (yes|no) [string]
	$origin = 'user';				//% videos from (user|3rdparty) [string]
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('orderBy', $args)) { $orderBy = $args['orderBy']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pageSize = (int)$args['pagination']; }
	if (true == array_key_exists('origin', $args)) { $origin = $args['origin']; }

	if ($pageSize <= 0) { $pageSize = 10; }

	switch ($orderBy) {
		case 'createdOn':	$ad = 'DESC';	break;
		case 'editedOn':	$ad = 'DESC';	break;
		case 'title':		$ad = 'ASC';	break;
		case 'videocount':	$ad = 'DESC';	break;
		default:			return ''; // no such sortable column
	}

	//----------------------------------------------------------------------------------------------
	//	count galleries, set start and end rows and load the recordset
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = 'videocount > 0';								//	do not show empty galleries
	$conditions[] = "origin='" . $kapenta->db->addMarkup($origin) . "'";		//	divide by content source

	$totalItems = $kapenta->db->countRange('videos_gallery', $conditions);
	$totalPages = ceil($totalItems / $pageSize);
	$start = $num * ($pageNo - 1);

	$range = $kapenta->db->loadRange('videos_gallery', '*', $conditions, $orderBy . ' ' . $ad, $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$linkBase = '%%serverPath%%videos/listallgalleries/orderby_';
	$pagination = "[[:theme::pagination::page=" . $pageNo 
				 . "::total=" . $totalPages . "::link=" . $linkBase . $orderBy . '/' . ":]]\n";

	$orderLinks = '[[:videos::orderlinks:]]';

	if ('yes' == $pagination) { $html .= $pagination . $orderLinks; }

	foreach($range as $item) { $html .= "[[:videos::summary::raUID=" . $item['UID'] . ":]]"; }
	if (($start + $num) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if ('yes' == $pagination) { $html .= $pagination . $orderLinks; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
