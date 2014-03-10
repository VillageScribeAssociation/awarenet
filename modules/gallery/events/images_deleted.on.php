<?

require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an image is deleted
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached
//arg: refUID - UID of object to which comment was attached
//arg: imageUID - UID of the new comment
//arg: imageTitle - text/html of comment

function gallery__cb_images_deleted($args) {
		global $kapenta;
		global $user;


	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('imageUID', $args) == false) { return false; }
	if (array_key_exists('imageTitle', $args) == false) { return false; }

	if ($args['refModule'] != 'gallery') { return false; }

	//----------------------------------------------------------------------------------------------
	//	update image count
	//----------------------------------------------------------------------------------------------
	$model = new Gallery_Gallery($args['refUID']);
	if (false == $model->loaded) { return false; }
	$model->updateImageCount();

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
