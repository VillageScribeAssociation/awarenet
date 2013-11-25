<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	require_once($kapenta->installPath . 'modules/videos/inc/videoset.class.php');

//--------------------------------------------------------------------------------------------------
//*	decrement the weight of a video
//--------------------------------------------------------------------------------------------------

	$return = 'uploadmultiple';

	//----------------------------------------------------------------------------------------------
	//	check permissions and request args
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Video not specified', true); }
	
	$model = new Videos_Video($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Video not found', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'editvideo', $model->refUID)) 
		{ $page->do403('You are not authorized to edit this video.'); }

	$set = new Videos_Videoset($model->refModule, $model->refModel, $model->refUID);
	$set->decWeight($model->UID);

	if (true == array_key_exists('return', $kapenta->request->args)) { $return = $kapenta->request->args['return']; }

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	$returnUrl = 'videos/uploadmultiple'
		. '/refModule_' . $model->refModule
		. '/refModel_' . $model->refModel
		. '/refUID_' . $model->refUID . '/';


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
