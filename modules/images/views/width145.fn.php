<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//	display a single image 145px wide
//--------------------------------------------------------------------------------------------------
// * $args['imageUID'] = overrides raUID
// * $args['raUID'] = record alias or UID
// * $args['link'] = link to larger version (yes|no)

function images_width145($args) { $args['size'] = 'width145'; return images__widthx($args); }

//--------------------------------------------------------------------------------------------------

?>
