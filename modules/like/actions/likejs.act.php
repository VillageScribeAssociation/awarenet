<?

	require_once($kapenta->installPath . 'modules/like/models/something.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action to create or reassert a 'like'
//--------------------------------------------------------------------------------------------------
//postarg: refModule - name of a kapenta module [string]
//postarg: refModel - type of object being endorsed [string]
//postarg: refUID - UID of object being endorsed [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { $kapenta->page->doXmlError('403'); }

	if (false == array_key_exists('refModule', $_POST)) { $kapenta->page->doXmlError('no refModule given'); }
	if (false == array_key_exists('refModel', $_POST)) { $kapenta->page->doXmlError('no refModel given'); }
	if (false == array_key_exists('refUID', $_POST)) { $kapenta->page->doXmlError('no refUID given'); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->doXmlError('No such module.'); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->doXmlError('No such object.'); }

	//----------------------------------------------------------------------------------------------
	//	check if the user already likes this item
	//----------------------------------------------------------------------------------------------
	$block = '[[:like::byuser'
	 . '::userUID=' . $kapenta->user->UID
	 . '::refModule=' . $refModule
	 . '::refModel=' . $refModel
	 . '::refUID=' . $refUID
	 . ':]]';

	$likeUID = $theme->expandBlocks($block);
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create or update like_something object
	//---------------------------------------------------------------------------------------------

	if ('' != $likeUID) {
		//------------------------------------------------------------------------------------------
		//	user already likes this, or has unliked this
		//------------------------------------------------------------------------------------------
		$model = new Like_Something($likeUID);
		if (true == $model->loaded) {
			$model->emotion = 'like';
			$model->cancelled = 'no';
			$report = $model->save();
		} else {
			$report .= "could not load object";
		}

	} else {
		//------------------------------------------------------------------------------------------
		//	this is a new like event
		//------------------------------------------------------------------------------------------
		$model = new Like_Something();
		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->emotion = 'like';
		$model->cancelled = 'no';
		$report = $model->save();

		//------------------------------------------------------------------------------------------
		//	raise event the first time somebody likes something
		//------------------------------------------------------------------------------------------
		if ('' == $report) {
			$args = array(
				'refModule' => $refModule,
				'refModel' => $refModel,
				'refUID' => $refUID
			);

			$kapenta->raiseEvent('*', 'like_something', $args);
		}

	}

	if ('' == $report) { echo "<ok/>"; }
	else { $kapenta->page->doXmlError($report); }

?>
