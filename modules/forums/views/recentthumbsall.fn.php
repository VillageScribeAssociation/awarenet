<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random forums images <--- NOT USED YET
//--------------------------------------------------------------------------------------------------
//arg: page - page we're at (default is 1) [string]
//opt: size - size of images, default is thumb [int]
//opt: num - maximum number of thumbs to show (most recent first) (default is 20) [string]

function forums_recentthumbsall($args) {
		global $kapenta;
		global $kapenta;
		global $user;

	$pageNo = 1;							//%	page number, starts at 1 [int]
	$num = 20;							//%	number number of items per page [int]
	$size = 'thumb';					//%	image size [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('page', $args)) { $pageNo = (int)$args['page']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	count total records owned by this module
	//----------------------------------------------------------------------------------------------
	//$sql = "select count(UID) as numRecords from Images_Image where refModule='forums'";	

	$conditions = array("refModule='forums'");
	$totalItems = $kapenta->db->countRange('images_image', $conditions)
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	make thumbs of images on this page
	//----------------------------------------------------------------------------------------------
	//$sql = "select * from Images_Image where refModule='forums' order by createdOn DESC " . $limit;	

	$start = (($pageNo - 1) * $num)
	$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'createdOn DESC', $num, $start);

	foreach ($range as $row) {
		//CONSIDER: load a model here
		$viewUrl = '%%serverPath%%forums/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title']
			  . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	$link = '%%serverPath%%forums/superforums/';

	$pagination .= "[[:theme::pagination::page=" . $pageNo
				 . "::total=" . $totalPages	. "::link=" . $link . ":]]\n";

	$html = $pagination . $html . $pagination;
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
