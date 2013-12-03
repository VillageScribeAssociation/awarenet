<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image scaled to fit the slideshow
//--------------------------------------------------------------------------------------------------
//arg: raUID - image recordAlias or UID [string]
//opt: imageUID - overrides raUID [string]
//opt: link - link to larger version (yes|no) [string]

function images_slide($args) { $args['size'] = 'slide'; return images__widthx($args); }

//--------------------------------------------------------------------------------------------------

?>

