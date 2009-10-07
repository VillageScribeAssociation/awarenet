<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a set of x thumbnails
//--------------------------------------------------------------------------------------------------
// * $args['page'] = results page
// * $args['refMod'] = module to list on
// * $args['num'] = number of images per page

function images_listall($args) {
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (authHas('images', 'list', '') == false) { return false; }
	$start = 0; $num = 30; $page = 1;
	if (array_key_exists('num', $args)) { $num = $args['num']; }
	if (array_key_exists('page', $args)) { 
		$page = $args['page']; 
		$start = ($page - 1) * $num;
	}

	//----------------------------------------------------------------------------------------------
	//	load the images
	//----------------------------------------------------------------------------------------------
	$list = dbLoadRange('images', '*', '', 'createdOn', $num, $start);
	foreach($list as $UID => $row) {
		$model = new Image();
		$model->loadArray($row);
		$html .= replaceLabels($model->extArray(), loadBlock('modules/images/summary.block.php'));
	}  
	return $html;	
	
}

//--------------------------------------------------------------------------------------------------

?>
