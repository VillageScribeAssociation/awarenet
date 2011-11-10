<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of gallery images
//--------------------------------------------------------------------------------------------------
//arg: raUID - Alias or UID of gallery (and not recordAlias) [string]
//opt: size - size to show thumbs (default is 'thumb') [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is no limit) [string]
//opt: UID - overrides raUID if present [string]
//opt: galleryUID - overrides UID if present [string]

function gallery_thumbs($args) {
	global $db;
	$limit = '';
	$html = '';
	$size = 'thumb';

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('galleryUID', $args)) { $args['raUID'] = $args['galleryUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $limit = (int)$args['num']; }

	$model = new Gallery_Gallery($args['raUID']);
	if (false == $model->loaded) { return '(gallery not found)'; }

	//---------------------------------------------------------------------------------------------
	//	load images
	//---------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='gallery'";
	$conditions[] = "refUID='" . $db->addMarkup($model->UID) . "'";

	$range = $db->loadRange('images_image', '*', $conditions, 'weight ASC', $limit, '');

	foreach($range as $item) {
		$viewUrl = '%%serverPath%%gallery/image/' . $item['alias'];

		//$thumbUrl = '%%serverPath%%images/' . $size . '/' . $row['UID'];
		// . "<img src='$thumbUrl' title='"
		// . $row['title'] . "' border='0' vspace='2px' hspace='2px' /></a>\n";

		$html .= ''
		 . "<a href='" . $viewUrl . "'>"
		 . "[[:images::show::size=$size::imageUID=" . $item['UID'] . "::pad=2::link=no:]]"
		 . "</a>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	dirty, but practical for now (views shouldn't change underlying data)
	//---------------------------------------------------------------------------------------------
	if ($model->imagecount != count($range)) {
		$model->imagecount = count($range);
		$model->save();
	}

	if (0 == $model->imagecount) {
		$html .= "<div class='inlinequote'>No images yet added.</div>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

