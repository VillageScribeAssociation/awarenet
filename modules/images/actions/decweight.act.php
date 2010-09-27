<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/imageset.class.php');

//--------------------------------------------------------------------------------------------------
//	increment the weight of an image
//--------------------------------------------------------------------------------------------------

	$return = 'uploadmultiple';

	//----------------------------------------------------------------------------------------------
	//	check permissions and request args
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('Image not specified', true); }
	
	$model = new Images_Image($req->ref);
	if (false == $model->loaded) { $page->do404('Image not found', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'editimage', $model->refUID)) 
		{ $page->do403('You are not authorized to edit this image.'); }

	$set = new Images_Imageset($model->refModule, $model->refModel, $model->refUID);
	$set->decWeight($model->UID);

	if (true == array_key_exists('return', $req->args)) { $return = $req->args['return']; }

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	$returnUrl = 'images-/uploadmultiple'
		. '/refModule_' . $model->refModule
		. '/refModel_' . $model->refModel
		. '/refUID_' . $model->refUID . '/';


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
