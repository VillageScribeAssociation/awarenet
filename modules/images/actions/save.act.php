<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to an image
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$return = 'uploadmultiple';
	$returnUrl = '';

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not specified'); }
	if ('saveImage' != $_POST['action']) { $kapenta->page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST))
		{ $kapenta->page->do404('reference object not specified', true); }


	$model = new Images_Image($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('No such image', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'images-edit', $model->refUID))
		{ $kapenta->page->do403('You are not authorized to edit this Image.'); }

	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

	//----------------------------------------------------------------------------------------------
	//	make the changes
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanString($value);		break;
			case 'licence':		$model->licence = $utils->cleanString($value);		break;
			case 'attribname':	$model->attribName = $utils->cleanString($value); 	break;
			case 'attriburl':	$model->attribUrl = $utils->cleanString($value); 	break;
			case 'caption':		$model->caption = $utils->cleanString($value); 		break;
			case 'category':	$model->category = $utils->cleanString($value); 	break;
		}
	}

	$report = $model->save();
	if ('' != $report) { $session->msg($report, 'bad'); }

	$session->msg("Saved image: " . $model->title, 'ok');

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	switch(strtolower($return)) {
		case 'show':
			$returnUrl = 'images/show/' . $model->alias;
			if ('' == $report) { $session->msg('Image updated.', 'ok'); }
			break;

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
