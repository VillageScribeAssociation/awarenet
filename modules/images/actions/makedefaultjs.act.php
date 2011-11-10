<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//*	set an image as the default for its owner
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'makeDefault' [string]
//postarg: UID - alias or UID of an Images_Image object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: enforce action, check permissions, etc
	if (false == array_key_exists('UID', $_POST)) {
		echo "<fail>Image not specified.</fail>"; die();
	}

	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { echo '<fail>Image not found</fail>'; die(); }

	//----------------------------------------------------------------------------------------------
	//	make default
	//----------------------------------------------------------------------------------------------
	$imgset = new Images_Images($model->refModule, $model->refModel, $model->refUID);
	$imgset->setDefault($model->UID);

	echo "<ok/>";

?>
