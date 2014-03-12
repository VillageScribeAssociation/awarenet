<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries for the nav 
//--------------------------------------------------------------------------------------------------
//arg: userUID - user whose galleries we wish to show [string]

function gallery_navlist($args) {
	global $kapenta;
	global $theme;
	global $kapenta;

	$num = 10;						//%	maximum number of galleries to show in nav [int]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	// TODO: permissions checks here

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "createdBy='" . $kapenta->db->addMarkup($args['userUID']) . "'";

	$range = $kapenta->db->loadRange('gallery_gallery', '*', $conditions, 'createdOn DESC', $num);
	
	//$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	if (0 == count($range) > 0) { return "<div class='inlinequote'>(no galleries)</div><br/>\n"; }

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

//--------------------------------------------------------------------------------------------------

?>
