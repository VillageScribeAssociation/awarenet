<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image 290px wide
//--------------------------------------------------------------------------------------------------
//arg: raUID - record alias or UID [string]
//opt: imageUID - overrides raUID [string]
//opt: link - link to larger version (yes|no) [string]

function images_width290($args) { $args['size'] = 'width290'; return images__widthx($args); }

//--------------------------------------------------------------------------------------------------

?>

