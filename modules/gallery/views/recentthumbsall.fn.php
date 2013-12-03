<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random gallery images
//--------------------------------------------------------------------------------------------------
//opt: size - default is 'thumb' [string]
//opt: page - page we're at, default is 1 (int) [string]
//opt: num - maximum number of thumbs to show, most recent first, default is 20 (int) [string]
//opt: pagination - display pagination bar (yes|no) [string]

function gallery_recentthumbsall($args) {
	global $page;
	global $db;
	global $user;

	$pageNo = 1; 					//%	page number as shown to users, starts at 1 [int]
	$num = 20; 						//%	number of items per page [int]
	$start = 0;						//%	offset in database results [int]
	$size = 'thumb'; 				//%	image size, as supported by images module [string]
	$pagination = 'yes';			//%	display pagination bar (yes|no) [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pagination', $args)) { $pagination = $args['pagination']; }

	if (true == array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page'];
		$start = (($pageNo - 1) * $num);
	}
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	count total records owned by this module
	//----------------------------------------------------------------------------------------------
	$conditions = array("refModule='gallery'");
	$totalItems = $db->countRange('images_image', $conditions);
	$totalPages = ceil($totalItems / $num);

	//----------------------------------------------------------------------------------------------
	//	make thumbs of images on this page
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('images_image', '*', $conditions, 'createdOn DESC', $num, $start);

	foreach ($range as $row) {
		$viewUrl = '%%serverPath%%gallery/image/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['alias'];
		$html .= ''
		 . "<a href='" . $viewUrl . "'>"
		 . "<img"
		 . " src='" . $thumbUrl . "'"
		 . " title='" . $row['title'] . "'"
		 . " border='0'"
		 . " vspace='2px'"
		 . " hspace='2px'"
		 . " width='100px'"
		 . " height='100px'"
		 . " class='rounded'"
		 . " style='background-color: #aaaaaa; display: inline;'/></a>\n";
	}

	if ('yes' == $pagination) {
		$pgBlock = ''
			. "[[:theme::pagination::page=" . $pageNo . "::total=" . $totalPages
			. "::link=%%serverPath%%gallery/supergallery/:]]\n";

		$html = $pagination . $html . $pagination;
	}

	if (($num + $start) > $totalItems) { $html .= "<!-- end of results -->"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
