<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	list som random gallery summaries formatted for the nav
//--------------------------------------------------------------------------------------------------
// * $args['num'] = max number to show (optional)

function gallery_randomgalleriesnav($args) {
	$num = 10; $html = '';
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	$sql = "select * from gallery order by RAND() limit 0," . sqlMarkup($num);
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