<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/views/show.fn.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image 560px wide
//--------------------------------------------------------------------------------------------------
//arg: raUID - record alias or UID [string]
//opt: imageUID - overrides raUID [string]
//opt: link - link to larger version (yes|no) [string]

function images_width560($args) {
	$args['size'] = 'width560';
	return images_show($args);
}

//--------------------------------------------------------------------------------------------------

?>

