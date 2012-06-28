<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	require_once($kapenta->installPath . 'modules/videos/models/videos.set.php');

//--------------------------------------------------------------------------------------------------
//*	set a video as the default for its owner
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'makeDefault' [string]
//postarg: UID - alias or UID of an Videos_Video object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: enforce action, check permissions, etc
	if (false == array_key_exists('UID', $_POST)) {
		echo "<fail>Video not specified.</fail>"; die();
	}

	$model = new Videos_Video($_POST['UID']);
	if (false == $model->loaded) { echo '<fail>Video not found</fail>'; die(); }

	//----------------------------------------------------------------------------------------------
	//	make default
	//----------------------------------------------------------------------------------------------
	$set = new Videos_Videos($model->refModule, $model->refModel, $model->refUID);
	$set->setDefault($model->UID);

	echo "<ok/>";

?>
