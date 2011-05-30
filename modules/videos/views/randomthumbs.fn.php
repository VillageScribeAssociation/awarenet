<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return thumbnails of random videos
//--------------------------------------------------------------------------------------------------
//opt: userUID - UID of a user (and not recordAlias) [string]
//opt: size - size to show thumbs (optional) [string]
//opt: num - maximum number of thumbs to show (most recent first) (default is all images) [string]
//: note the direct use of images table - TODO: work around this

function videos_randomthumbs($args) {
	global $db;
	$limit = 30;			//%	maximum number of thumbnails to display [int]
	$html = '';				//%	return value [string]
	$size = 'thumbsm';		//%	image size [string]
	$userUID = '';			//%	UID of a user, if showing only a single user's videos

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { $userUID = $args['userUID']; }
	if (true == array_key_exists('size', $args)) { $size = $args['size']; }
	if (true == array_key_exists('num', $args)) { $limit = (int)$args['num']; }

	//---------------------------------------------------------------------------------------------
	//	get random image UIDs
	//---------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "refModel='" . $db->addMarkup('videos_gallery') . "'";
	$conditions[] = "refModule='videos'";
	if ('' != $userUID) { $conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'"; }

	$range = $db->loadRange('videos_video', '*', $conditions, 'RAND()', $limit, '');

	foreach($range as $item) {
		$viewUrl = '%%serverPath%%videos/play/' . $item['alias'];
		$thumb = "[[:images::default::link=no::size=thumb90::"
		 . "refModule=videos::refModel=videos_video::refUID=" . $item['UID'] . ":]]";

		$html .= "<a href='" . $viewUrl . "'>$thumb</a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

