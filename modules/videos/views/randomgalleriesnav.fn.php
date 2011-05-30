<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list some random gallery summaries (formatted for the nav)
//--------------------------------------------------------------------------------------------------
// opt: num = max number to show, default is 10 (int) [string]

function videos_randomgalleriesnav($args) {
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
	$conditions = array('videocount > 0');
	$range = $db->loadRange('videos_gallery', '*', $conditions, 'RAND()', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	//$sql = "SELECT * FROM Videos_Gallery "
	//	   . "WHERE videocount > 0 "
	//	   . "ORDER BY RAND() limit 0," . $db->addMarkup($num);

	$block = $theme->loadBlock('modules/videos/views/summarynav.block.php');

	foreach ($range as $row) {
		$model = new Videos_Gallery();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['galleryUID'] = $row['UID'];
		$html .= $theme->replaceLabels($labels, $block);
	}

	return $html;
}



?>

