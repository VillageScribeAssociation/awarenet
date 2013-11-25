<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list some random gallery summaries (formatted for the nav)
//--------------------------------------------------------------------------------------------------
//DEPRECATED: replace this block with something anchored in user context
//opt: num = max number to show, default is 10 (int) [string]

function gallery_randomgalleriesnav($args) {
	global $db;
	global $theme;
	global $kapenta;

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

	$orderBy = 'RAND()';
	if ('SQLite' == $kapenta->registry->get('db.driver')) { $orderBy = 'RANDOM()'; }

	$range = $db->loadRange('gallery_gallery', '*', $conditions, $orderBy, $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	//$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	foreach ($range as $item) {
		//$model = new Gallery_Gallery();
		//$model->loadArray($row);
		//$labels = $model->extArray();
		//$labels['galleryUID'] = $row['UID'];
		//$html .= $theme->replaceLabels($labels, $block);
		$html .= "[[:gallery::summarynav::galleryUID=" . $item['UID'] . ":]]";
	}

	return $html;
}



?>

