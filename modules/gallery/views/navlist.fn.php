<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all gallerys for the nav 
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = user whose galleries we wish to show

function gallery_navlist($args) {
	if (array_key_exists('userUID', $args) == false) { return false; }
	$html = '';

	$sql = "select * from gallery "
		 . "where parent='root' and createdBy='" . sqlMarkup($args['userUID']) . "' "
		 . "order by title"; 

	$block = loadBlock('modules/gallery/views/summarynav.block.php');

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

			$model = new Gallery();
			$model->loadArray($row);
			$labels = $model->extArray();
			$labels['galleryUID'] = $row['UID'];

			$html .= replaceLabels($labels, $block);
		}
	} else {
		$html = "(none)<br/>\n";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>