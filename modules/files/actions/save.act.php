<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Files_File object
//--------------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('action', $_POST))
	   AND ('savefile' == $_POST['action'])
	   AND (true == array_key_exists('UID', $_POST)) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$model = new Files_File($_POST['UID']);
		if (false == $model->loaded) { $page->do404(); }
		
		if (false == $user->authHas($model->refModule, $model->refModel, 'files-edit', $model->refUID)) 
			{ $page->do403(''); }

		//TODO: more permission options here

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		//TODO: more checking/cleanign here *security risk
		$model->title = $_POST['title'];
		$model->caption = $_POST['caption'];
		$model->licence = $_POST['licence'];
		$model->attribName = $_POST['attribName'];
		$model->attribURL = $_POST['attribURL'];
		
		$report = $model->save();
		if ('' != $report) { $session->msg('Changes could not be saved:<br/>' . $report, 'bad');

		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (true == array_key_exists('return', $_POST)) {
			if ('uploadmultiple' == $_POST['return']) {
				$retUrl = 'files/uploadmultiple'
					 . '/refModule_' . $model->refModule 
					 . '/refModel_' . $model->refModel
					 . '/refUID_' . $model->refUID . '/';

				$page->do302($retUrl);
			}
		}
		$page->do302('files/edit/' . $model->alias);
	}

?>
