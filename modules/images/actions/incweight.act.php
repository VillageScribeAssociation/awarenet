<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//*	increment the weight of an image
//--------------------------------------------------------------------------------------------------

	$return = 'uploadmultiple';
	$returnUrl = '';

	//----------------------------------------------------------------------------------------------
	//	check permissions and request args
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('Image not specified', true); }
	
	$model = new Images_Image($req->ref);
	if (false == $model->loaded) { $page->do404('Image not found', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'editimage', $model->refUID)) 
		{ $page->do403('You are not authorized to edit this image.'); }


	$returnUrl = 'images/edit/' . $model->alias;

	$set = new Images_Images($model->refModule, $model->refModel, $model->refUID);
	$set->incWeight($model->UID);

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

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
			$page->do302('images/edit/' . $model->alias);
			break;
	}
		
	$page->do302($returnUrl);		

?>
