<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random gallery images
//--------------------------------------------------------------------------------------------------
//opt: size - default is 'thumb' [string]
//opt: page - page we're at, default is 1 (int) [string]
//opt: num - maximum number of thumbs to show, most recent first, default is 20 (int) [string]

function gallery_recentthumbsall($args) {
	global $page, $db, $user;
	$pageNo = 1; 					//%	page number as shown to users, starts at 1 [int]
	$num = 20; 						//%	number of items per page [int]
	$size = 'thumb'; 				//%	image size, as supported by images module [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }
	if (array_key_exists('page', $args) == true) { $pageNo = (int)$args['page']; }
	if (array_key_exists('num', $args) == true) { $num = (int)$args['num']; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	count total records owned by this module
	//----------------------------------------------------------------------------------------------
	$conditions = array("refModule='gallery'");
	$totalItems = $db->countRange('images_image', $conditions);
	$totalPages = ceil($totalItems / $num);
	//$sql = "select count(UID) as numRecords from Images_Image where refModule='gallery'";	

	//----------------------------------------------------------------------------------------------
	//	make thumbs of images on this page
	//----------------------------------------------------------------------------------------------
	$start = (($pageNo - 1) * $num);
	$range = $db->loadRange('images_image', '*', $conditions, 'createdOn DESC', $num, $start);

	foreach ($range as $row) {
		$viewUrl = '%%serverPath%%gallery/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= "<a href='" . $viewUrl . "'>"
			  . "<img src='" . $thumbUrl . "' title='" . $row['title']
			  . "' border='0' vspace='2px' hspace='2px' /></a>\n";
	}

	$pagination = "[[:theme::pagination::page=" . $pageNo . "::total=" . $totalPages
				. "::link=%%serverPath%%gallery/supergallery/:]]\n";

	$html = $pagination . $html . $pagination;
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
