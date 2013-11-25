<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	require_once($kapenta->installPath . 'modules/videos/inc/videoset.class.php');

//--------------------------------------------------------------------------------------------------
//*	increment the weight of a video
//--------------------------------------------------------------------------------------------------

	$return = 'uploadmultiple';
	$returnUrl = '';

	//----------------------------------------------------------------------------------------------
	//	check permissions and request args
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Video not specified', true); }
	
	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Video not found', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'editvideo', $model->refUID)) 
		{ $page->do403('You are not authorized to edit this video.'); }


	$returnUrl = 'videos/edit/' . $model->alias;

	$set = new Videos_Videoset($model->refModule, $model->refModel, $model->refUID);
	$set->incWeight($model->UID);

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	switch(strtolower($return)) {
		case 'uploadmultiple':
			$returnUrl = 'videos/uploadmultiple'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;

		case 'uploadsingle':
			$returnUrl = 'videos/uploadsingle'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;

		default:
			$page->do302('videos/edit/' . $model->alias);
			break;
	}
		
	$page->do302($returnUrl);		

?>
