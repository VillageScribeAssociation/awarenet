<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list some random gallery summaries (formatted for the nav)
//--------------------------------------------------------------------------------------------------
// opt: num = max number to show (default is 10)

function gallery_randomgalleriesnav($args) {
	$num = 10; $html = '';
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	$sql = "select * from gallery where imagecount > 0 order by RAND() limit 0," . sqlMarkup($num);
	$block = loadBlock('modules/gallery/views/summarynav.block.php');

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$model = new Gallery();
		$model->loadArray($row);
		$labels = $model->extArray();
		$labels['galleryUID'] = $row['UID'];
		$html .= replaceLabels($labels, $block);
	}

	return $html;
}



?>

