<?

//--------------------------------------------------------------------------------------------------
//*	display n most recent images at the specified size
//--------------------------------------------------------------------------------------------------
//opt: num - number of images to show, default is 25 (int) [string]
//opt: size - image size to use [string]
//opt: pageNo - page number [string]

function images_recent($args) {
	global $kapenta;
	global $theme;

	$pageNo = 1;					//%	result page to show [string]
	$start = 0;						//%	result position to start from [string]

	$size = 'thumbsm';				//%	size to display [string]
	$num = 25;						//%	number of images per page [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (0 == $num) { return '(0 images per page)'; }

	if (true == array_key_exists('pageNo', $args)) {
		$pageNo = (int)$args['pageNo'];
		$start = $num * ($pageNo - 1);
	}

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	//^ add any conditions here

	$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'createdOn DESC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($range as $item) {
		$html .= ''
		 . "<a href='%%serverPath%%images/show/" . $item['alias'] . "'>"
		 . "<img"
		 . " src='%%serverPath%%images/" . $size . '/' . $item['alias'] . "'"
		 . " title='" . $item['title'] . "'"
		 . " border='0' vspace='5px' hspace='5px' "
		 . "/></a>";
	}

	return $html;
}

?>
