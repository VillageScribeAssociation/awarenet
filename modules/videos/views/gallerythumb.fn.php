<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a cover image for the first video in a gallery
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Gallery object [string]
//opt: galleryUID - overrides raUID if present [string]
//opt: size - size of image, default is thumb [string]

function videos_gallerythumb($args) {
	global $theme;
	$html = '';					//%	return value [string:html]
	$size = 'thumb';			//%	image size [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('galleryUID', $args)) { $args['raUID'] = $args['galleryUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(gallery not specified)'; }

	$model = new Videos_Gallery($args['raUID']);
	if (false == $model->loaded) { return '(gallery not found)'; }

	if (true == array_key_exists('size', $args)) {$size = $args['size']; }

	//----------------------------------------------------------------------------------------------
	//	load first video from the database
	//----------------------------------------------------------------------------------------------
	$range = $model->loadVideos();
	foreach($range as $item) {
		$html = "[[:images::default::size=$size::link=no"
			. "refModule=videos::refModel=videos_video::refUID=" . $item['UID'] . ":]]";

		return $html;
	}

	return '(no thumb)';
}

?>
