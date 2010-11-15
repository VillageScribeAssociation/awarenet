<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries for the nav 
//--------------------------------------------------------------------------------------------------
//arg: userUID - user whose galleries we wish to show [string]

function videos_navlist($args) {
	global $db, $theme, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	load from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'";

	//$sql = "SELEcT * FROM Videos_Gallery "
	//	   . "WHERE createdBy='" . $db->addMarkup($args['userUID']) . "' "
	//	   . "ORDER BY title";

	$range = $db->loadRange('Videos_Gallery', '*', $conditions, 'title');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	if (count($range) > 0) {
		foreach ($range as $row) {
			$model = new Videos_Gallery();
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
