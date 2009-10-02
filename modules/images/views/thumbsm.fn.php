<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//	display a single image at some size or other
//--------------------------------------------------------------------------------------------------
// * $args['imageUID'] = overrides raUID
// * $args['raUID'] = record alias or UID
// * $args['link'] = link to larger version (yes|no)

function images_thumbsm($args) { $args['size'] = 'thumbsm'; return images__widthx($args); }
function images_thumb($args) { $args['size'] = 'thumb'; return images__widthx($args); }
function images_thumb90($args) { $args['size'] = 'thumb90'; return images__widthx($args); }
function images_slide($args) { $args['size'] = 'slide'; return images__widthx($args); }
function images_width100($args) { $args['size'] = 'width100'; return images__widthx($args); }
function images_width145($args) { $args['size'] = 'width145'; return images__widthx($args); }
function images_width200($args) { $args['size'] = 'width200'; return images__widthx($args); }
function images_width290($args) { $args['size'] = 'width290'; return images__widthx($args); }
function images_width300($args) { $args['size'] = 'width300'; return images__widthx($args); }
function images_width560($args) { $args['size'] = 'width560'; return images__widthx($args); }
function images_width570($args) { $args['size'] = 'width570'; return images__widthx($args); }

//--------------------------------------------------------------------------------------------------

?>
