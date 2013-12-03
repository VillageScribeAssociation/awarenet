<?

	require_once($kapenta->installPath . 'modules/images/views/show.fn.php');

//--------------------------------------------------------------------------------------------------
//|	alias of images_show
//--------------------------------------------------------------------------------------------------
//arg: raUID - record alias or UID [string]
//opt: size - name of an image size, default is 'widthcontent' [string]
//opt: imageUID - overrides raUID [string]
//opt: link - link to larger version (yes|no) [string]

function images_s($args) {
	return images_show($args);
}

//--------------------------------------------------------------------------------------------------

?>

