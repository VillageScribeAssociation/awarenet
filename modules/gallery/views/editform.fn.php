<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]

function gallery_editform($args) {
	if (authHas('gallery', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new gallery($args['raUID']);
	$ext = $model->extArray();
	$ext['descriptionJs64'] = base64EncodeJs('descriptionJs64', $ext['description']);
	return replaceLabels($ext, loadBlock('modules/gallery/views/editform.block.php'));
}


//--------------------------------------------------------------------------------------------------

?>

