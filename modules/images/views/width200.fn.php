<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//	display a single image 200px wide
//--------------------------------------------------------------------------------------------------
// * $args['imageUID'] = overrides raUID
// * $args['raUID'] = record alias or UID
// * $args['link'] = link to larger version (yes|no)

function images_width200($args) { $args['size'] = 'width200'; return images__widthx($args); }

//--------------------------------------------------------------------------------------------------

?>
