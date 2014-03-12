<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a set of x thumbnails
//--------------------------------------------------------------------------------------------------
//opt: page - results page (default is 1) [string]
//opt: num - number of images per page (default is 30) [string]

function images_listall($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $kapenta;

	$num = 30;							//%	number of items per page [int]
	$pageNo = 1;						//%	page number, starts from 1 [int]
	$start = 0;							//%	position in SQL results [int]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('images', 'images_image', 'show')) { return false; }

	if (array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (array_key_exists('page', $args)) { 
		$pageNo = (int)$args['page']; 
		$start = ($pageNo - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	load page of items from the database and make the block
	//----------------------------------------------------------------------------------------------
	//TODO: add any conditions here
	$range = $kapenta->db->loadRange('images_image', '*', '', 'createdOn', $num, $start);
	$block = $theme->loadBlock('modules/images/summary.block.php');

	foreach($range as $UID => $row) {
		$model = new Images_Image();
		$model->loadArray($row);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}  
	return $html;	
	
}

//--------------------------------------------------------------------------------------------------

?>
