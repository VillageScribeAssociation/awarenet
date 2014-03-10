<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');
	//require_once($kapenta->installPath . 'modules/videos/inc/videos__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a video
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
	if ('saveVideo' != $_POST['action']) { $kapenta->page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST))
		{ $kapenta->page->do404('reference object not specified', true); }

	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

	$model = new Videos_Video($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('No such video', true); }

	if (false == $user->authHas($model->refModule, $model->refModel, 'videos-edit', $model->refUID))
		{ $kapenta->page->do403('You are not authorized to edit this video.'); }

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

	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	switch(strtolower($return)) {
		case 'uploadmultiple':
			$returnUrl = 'videos/uploadmultiple'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;		//..........................................................................

		case 'uploadsingle':
			$returnUrl = 'videos/uploadsingle'
				. '/refModule_' . $model->refModule
				. '/refModel_' . $model->refModel
				. '/refUID_' . $model->refUID . '/';
			break;		//..........................................................................

		case 'player':
			$returnUrl = 'videos/play/' . $model->alias;
			break;		//..........................................................................

		case 'editmodal':
			$returnUrl = 'videos/editvideo/' . $model->alias;
			break;		//..........................................................................

		default:
			$kapenta->page->do302('videos/edit/' . $model->alias);
			break;
	}
		
	$kapenta->page->do302($returnUrl);

?>
