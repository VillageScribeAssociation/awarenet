<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//*	decrement the weight of an image
//--------------------------------------------------------------------------------------------------

	$return = 'uploadmultiple';

	//----------------------------------------------------------------------------------------------
	//	check permissions and request args
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Image not specified', true); }
	
	$model = new Images_Image($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Image not found', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'editimage', $model->refUID)) 
		{ $kapenta->page->do403('You are not authorized to edit this image.'); }

	$set = new Images_Images($model->refModule, $model->refModel, $model->refUID);
	$set->decWeight($model->UID);

	if (true == array_key_exists('return', $kapenta->request->args)) { $return = $kapenta->request->args['return']; }

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	$returnUrl = 'images/uploadmultiple'
		. '/refModule_' . $model->refModule
		. '/refModel_' . $model->refModel
		. '/refUID_' . $model->refUID . '/';	// redundant, remove?


	switch(strtolower($return)) {
		
		case 'uploadmultiple':
			$returnUrl = 'images/uploadmultiple'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;

		case 'uploadsingle':
			$returnUrl = 'images/uploadsingle'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;

		default:
			$kapenta->page->do302('images/edit/' . $model->alias);
			break;
	}
		
	$kapenta->page->do302($returnUrl);		

?>
