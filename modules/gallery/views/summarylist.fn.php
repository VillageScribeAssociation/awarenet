<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries belonging to a specified user
//--------------------------------------------------------------------------------------------------
//opt: orderBy - field to order by, default is title (title|imagecount|createdOn|editedOn) [string]
//opt: pageNo - page number to display, default is '1' [string]
//opt: pageSize - number of records per page, default '10' [string]
//: galleries which do not contain any images are not displayed
//returns: html list [string]

function gallery_summarylist($args) {
	$orderBy = 'title';	$pageNo = 1; $pageSize = 10; $html = ''; $ad = 'DESC';

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('orderBy', $args) == true) { $orderBy = $args['orderBy']; }
	if (array_key_exists('pageNo', $args) == true) { $pageNo = (int)$args['pageNo']; }
	if (array_key_exists('pageSize', $args) == true) { $pageSize = (int)$args['pageSize']; }

	if ($pageSize <= 0) { $pageSize = 10; }

	switch ($orderBy) {
		case 'createdOn':	$ad = 'DESC';	break;
		case 'editedOn':	$ad = 'DESC';	break;
		case 'title':		$ad = 'ASC';	break;
		case 'imagecount':	$ad = 'DESC';	break;
		default:			return ''; // no such sortable column
	}

	//---------------------------------------------------------------------------------------------
	//	count galleries, set start and end rows and load the recordset
	//---------------------------------------------------------------------------------------------
	$conditions = array('imagecount > 0');	// do not show galleries with no images
	$numRows = dbCountRange('gallery', $conditions);
	$numPages = ceil($numRows / $pageSize);
	$startRow = $pageSize * ($pageNo - 1);

	$range = dbLoadRange('gallery', '*', $conditions, $orderBy . ' ' . $ad, $pageSize, $startRow);

	//---------------------------------------------------------------------------------------------
	//	render html
	//---------------------------------------------------------------------------------------------

	$linkBase = '%%serverPath%%gallery/listall/orderby_';
	$pagination .= "[[:theme::pagination::page=" . $pageNo 
				 . "::total=" . $numPages . "::link=" . $linkBase . $orderBy . '/' . ":]]\n";

	$orderLinks .= "<table noborder width='100%'><tr><td bgcolor='#dddddd'>" 
				. "&nbsp;&nbsp; list by: "
				. "<a href='" . $linkBase . "title'>[title]</a> "
				. "<a href='" . $linkBase . "imagecount'>[number of images]</a> "
				. "<a href='" . $linkBase . "createdOn'>[creation date]</a>"
				. "</td></tr></table><hr/>";

	$html .= $pagination . $orderLinks;

	if (count($range) > 0) {
		foreach($range as $row) { $html .= "[[:gallery::summary::raUID=" . $row['UID'] . ":]]"; }
	} else { 
		$html .= "(no galleries match criteria)"; 
	}

	$html .= $pagination . $orderLinks;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

