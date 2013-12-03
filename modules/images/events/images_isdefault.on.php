<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a module sets a default image for something
//--------------------------------------------------------------------------------------------------
//+	This is inelegant, TODO: come up with a better solution

function images__cb_images_isdefault($args) {
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	$model = new Images_Image($args['imageUID']);
	if (false == $model->loaded) { return false; }

	$set = new Images_Images($model->refModule, $model->refModel, $model->refUID);
	$check = $set->setDefault($model->UID);
	return $check;
}


?>
