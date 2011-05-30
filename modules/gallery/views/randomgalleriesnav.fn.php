<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list some random gallery summaries (formatted for the nav)
//--------------------------------------------------------------------------------------------------
// opt: num = max number to show, default is 10 (int) [string]

function gallery_randomgalleriesnav($args) {
	global $db, $theme;
	$num = 10; 
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load x random galleries from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array('imagecount > 0');
	$range = $db->loadRange('gallery_gallery', '*', $conditions, 'RAND()', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	//$sql = "select * from gallery where imagecount > 0 order by RAND() limit 0," . $db->addMarkup($num);
	$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	foreach ($range as $row) {
		$model = new Gallery_Gallery();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['galleryUID'] = $row['UID'];
		$html .= $theme->replaceLabels($labels, $block);
	}

	return $html;
}



?>

