<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries for the nav 
//--------------------------------------------------------------------------------------------------
//arg: userUID - user whose galleries we wish to show [string]

function gallery_navlist($args) {
	global $db, $theme, $user;
	$html = '';

	if (array_key_exists('userUID', $args) == false) { return false; }

	//$sql = "select * from gallery "
	//	 . "where parent='root' and createdBy='" . $db->addMarkup($args['userUID']) . "' "
	//	 . "order by title"; 

	$conditions = array();
	$conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'";
	$conditions[] = "parent='root'";

	$range = $db->loadRange('Gallery_Gallery', '*', $conditions, 'title');
	
	$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	if (count($range) > 0) {
		foreach ($range as $row) {
			$model = new Gallery_Gallery();
			$model->loadArray($row);
			$labels = $model->extArray();
			$labels['galleryUID'] = $row['UID'];
			$html .= $theme->replaceLabels($labels, $block);
		}

	} else { $html = "<div class='inlinequote'>(no galleries)</div><br/>\n"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

